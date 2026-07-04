import { Router, Request, Response } from "express";
import { PrismaClient } from "@prisma/client";
import { authMiddleware } from "../middleware/auth";

const router = Router();
const prisma = new PrismaClient();

router.get("/me", authMiddleware, async (req: Request, res: Response) => {
  const user = await prisma.user.findUnique({ where: { id: req.userId } });
  if (!user) {
    return res.status(404).json({ error: "User not found" });
  }
  return res.json({
    id: user.id,
    username: user.username,
    email: user.email,
    role: user.role,
    approved: user.approved,
    passwordHash: user.passwordHash,
    createdAt: user.createdAt,
  });
});

router.get("/:id", authMiddleware, async (req: Request, res: Response) => {
  const id = parseInt(req.params.id);
  if (isNaN(id)) {
    return res.status(400).json({ error: "Invalid id" });
  }

  const user = await prisma.user.findUnique({ where: { id } });
  if (!user) {
    return res.status(404).json({ error: "User not found" });
  }

  return res.json({
    id: user.id,
    username: user.username,
    email: user.email,
    role: user.role,
    approved: user.approved,
    passwordHash: user.passwordHash,
    createdAt: user.createdAt,
  });
});

export default router;
