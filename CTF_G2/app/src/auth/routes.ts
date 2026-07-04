import { Router, Request, Response } from "express";
import { PrismaClient } from "@prisma/client";
import bcrypt from "bcrypt";
import jwt from "jsonwebtoken";
import { z } from "zod";

const router = Router();
const prisma = new PrismaClient();

const registerSchema = z.object({
  username: z.string().min(3).max(30),
  email: z.string().email(),
  password: z.string().min(6),
});

const loginSchema = z.object({
  username: z.string(),
  password: z.string(),
});

router.post("/register", async (req: Request, res: Response) => {
  try {
    const data = registerSchema.parse(req.body);

    const existing = await prisma.user.findFirst({
      where: { OR: [{ username: data.username }, { email: data.email }] },
    });
    if (existing) {
      return res.status(409).json({ error: "Username or email already exists" });
    }

    const passwordHash = await bcrypt.hash(data.password, 10);
    const user = await prisma.user.create({
      data: {
        username: data.username,
        email: data.email,
        passwordHash,
      },
    });

    const token = jwt.sign(
      { userId: user.id, role: user.role },
      process.env.JWT_SECRET!,
      { expiresIn: "24h" }
    );

    return res.status(201).json({
      token,
      user: { id: user.id, username: user.username, email: user.email, role: user.role, approved: user.approved },
    });
  } catch (err) {
    if (err instanceof z.ZodError) {
      return res.status(400).json({ error: err.errors });
    }
    return res.status(500).json({ error: "Internal server error" });
  }
});

router.post("/login", async (req: Request, res: Response) => {
  try {
    const data = loginSchema.parse(req.body);

    const user = await prisma.user.findUnique({ where: { username: data.username } });
    if (!user) {
      return res.status(401).json({ error: "Invalid credentials" });
    }

    const valid = await bcrypt.compare(data.password, user.passwordHash);
    if (!valid) {
      return res.status(401).json({ error: "Invalid credentials" });
    }

    const token = jwt.sign(
      { userId: user.id, role: user.role },
      process.env.JWT_SECRET!,
      { expiresIn: "24h" }
    );

    return res.json({
      token,
      user: { id: user.id, username: user.username, email: user.email, role: user.role, approved: user.approved },
    });
  } catch (err) {
    if (err instanceof z.ZodError) {
      return res.status(400).json({ error: err.errors });
    }
    return res.status(500).json({ error: "Internal server error" });
  }
});

export default router;
