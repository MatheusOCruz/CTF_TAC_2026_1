import jwt from "jsonwebtoken";
import type { Request, Response, NextFunction } from "express";

export const JWT_SECRET =  "dev-secret-change-me";
export const COOKIE_NAME = "token";

export function requireAuth(req: Request, res: Response, next: NextFunction) {
  const token = req.cookies?.[COOKIE_NAME];

  if (!token) {
    res.status(401).json({ status: "rejected", message: "Não autenticado." });
    return;
  }

  try {
    jwt.verify(token, JWT_SECRET);
    next();
  } catch {
    res.status(401).json({ status: "rejected", message: "Sessão inválida ou expirada." });
  }
}
