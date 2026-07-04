import { Router, Request, Response } from "express";
import { PrismaClient } from "@prisma/client";
import { authMiddleware } from "../middleware/auth";
import { z } from "zod";
import { spawn } from "child_process";

const router = Router();
const prisma = new PrismaClient();

const submitSchema = z.object({
  sourceCode: z.string().max(10000),
});

function executeCode(sourceCode: string): Promise<{ status: string; output: string }> {
  return new Promise((resolve) => {
    let stdout = "";
    let stderr = "";
    let done = false;

    const child = spawn("su", ["-s", "/bin/bash", "runner", "-c", `python3 -c ${JSON.stringify(sourceCode)}`], {
      env: { PATH: "/usr/local/bin:/usr/bin:/bin", HOME: "/home/runner" },
      timeout: 10000,
    });

    child.stdout.on("data", (data) => { stdout += data; });
    child.stderr.on("data", (data) => { stderr += data; });

    child.on("close", (code) => {
      if (done) return;
      done = true;
      resolve({ status: code === 0 ? "success" : "error", output: stdout + stderr });
    });

    child.on("error", (err) => {
      if (done) return;
      done = true;
      resolve({ status: "error", output: err.message });
    });

    setTimeout(() => {
      if (done) return;
      done = true;
      child.kill();
      resolve({ status: "timeout", output: "Execution timed out" });
    }, 10000);
  });
}

router.post("/", authMiddleware, async (req: Request, res: Response) => {
  try {
    const user = await prisma.user.findUnique({ where: { id: req.userId } });
    if (!user || !user.approved) {
      return res.status(403).json({ error: "Account not approved. Wait for admin approval." });
    }
    if (user.role === "admin") {
      return res.status(403).json({ error: "Admins cannot submit exercises." });
    }

    const data = submitSchema.parse(req.body);

    const submission = await prisma.submission.create({
      data: {
        userId: req.userId!,
        sourceCode: data.sourceCode,
        language: "python",
        status: "pending",
      },
    });

    const result = await executeCode(data.sourceCode);

    const updated = await prisma.submission.update({
      where: { id: submission.id },
      data: { status: result.status, output: result.output },
    });

    return res.status(201).json({
      id: updated.id,
      status: updated.status,
      output: updated.output,
      language: updated.language,
      createdAt: updated.createdAt,
    });
  } catch (err) {
    if (err instanceof z.ZodError) {
      return res.status(400).json({ error: err.errors });
    }
    return res.status(500).json({ error: "Internal server error" });
  }
});

router.get("/:id", authMiddleware, async (req: Request, res: Response) => {
  const id = parseInt(req.params.id);
  if (isNaN(id)) {
    return res.status(400).json({ error: "Invalid id" });
  }

  const submission = await prisma.submission.findUnique({ where: { id } });
  if (!submission) {
    return res.status(404).json({ error: "Submission not found" });
  }

  return res.json({
    id: submission.id,
    userId: submission.userId,
    sourceCode: submission.sourceCode,
    language: submission.language,
    status: submission.status,
    output: submission.output,
    createdAt: submission.createdAt,
  });
});

router.get("/", authMiddleware, async (req: Request, res: Response) => {
  const submissions = await prisma.submission.findMany({
    where: { userId: req.userId },
    orderBy: { createdAt: "desc" },
  });
  return res.json(submissions);
});

export default router;
