import { Router, Request, Response } from "express";
import { PrismaClient } from "@prisma/client";
import { authMiddleware, adminMiddleware } from "../middleware/auth";

const router = Router();
const prisma = new PrismaClient();

router.use(authMiddleware, adminMiddleware);

router.get("/users", async (_req: Request, res: Response) => {
  const users = await prisma.user.findMany({
    select: {
      id: true,
      username: true,
      email: true,
      role: true,
      approved: true,
      createdAt: true,
    },
  });
  return res.json(users);
});

router.post("/approve/:id", async (req: Request, res: Response) => {
  const id = parseInt(req.params.id);
  if (isNaN(id)) {
    return res.status(400).json({ error: "Invalid id" });
  }

  const user = await prisma.user.findUnique({ where: { id } });
  if (!user) {
    return res.status(404).json({ error: "User not found" });
  }

  const updated = await prisma.user.update({
    where: { id },
    data: { approved: true },
  });

  return res.json({ message: "User approved", user: { id: updated.id, username: updated.username, approved: updated.approved } });
});

router.post("/deny/:id", async (req: Request, res: Response) => {
  const id = parseInt(req.params.id);
  if (isNaN(id)) {
    return res.status(400).json({ error: "Invalid id" });
  }

  const user = await prisma.user.findUnique({ where: { id } });
  if (!user) {
    return res.status(404).json({ error: "User not found" });
  }

  const updated = await prisma.user.update({
    where: { id },
    data: { approved: false },
  });

  return res.json({ message: "User denied", user: { id: updated.id, username: updated.username, approved: updated.approved } });
});

export default router;
