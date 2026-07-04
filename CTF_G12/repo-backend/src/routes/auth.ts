import { Router, type Request, type Response } from "express";
import jwt from "jsonwebtoken";
import { JWT_SECRET, COOKIE_NAME, requireAuth } from "../middlewares/auth";

const router = Router();

/* Segredos só no servidor — em produção venham de variáveis de ambiente */
const unit_id_saved = "apexpredator";
const access_key_saved = "duck";

const COOKIE_OPTIONS = {
  httpOnly: true,
  sameSite: "lax" as const,
  secure: process.env.NODE_ENV === "production",
  maxAge: 1000 * 60 * 60, // 1h
};

/* POST /api/login - Entra no sistema dadas as credenciais */
router.post("/login", (req: Request, res: Response) => {
  const unit_id = String(req.body?.unit_id ?? "").trim();
  const access_key = String(req.body?.access_key ?? "").trim();

  if ((unit_id === unit_id_saved) && (access_key === access_key_saved)) {
    const token = jwt.sign({ sub: unit_id }, JWT_SECRET, { expiresIn: "1h" });
    res.cookie(COOKIE_NAME, token, COOKIE_OPTIONS);
    res.json({
      status: "ok",
      message: "LOGIN BEM SUCEDIDO.",
      // Cookie é httpOnly (front não consegue ler) — vai também no corpo pra
      // o front poder repassar pro academy.repo via /login-bridge.
      token,
    });
    return;
  } else if ((unit_id === unit_id_saved) && (access_key !== access_key_saved)) {
    res.status(400).json({
      status: "rejected",
      message: "Incorrect Password",
    });
    return;
  } else if ((unit_id !== unit_id_saved) && (access_key === access_key_saved)) {
    res.status(400).json({
      status: "rejected",
      message: "Incorrect User",
    });
    return;
  } else {
    res.status(400).json({
      status: "rejected",
      message: "Incorrect Credentials",
    });
  }
});

/* POST /api/logout - Encerra a sessão */
router.post("/logout", (_req: Request, res: Response) => {
  res.clearCookie(COOKIE_NAME, COOKIE_OPTIONS);
  res.json({ status: "ok", message: "User Logged Out" });
});

/* GET /api/verify - Usado pelo Nginx (auth_request) para checar se o cookie é válido
   antes de servir páginas estáticas protegidas */
router.get("/verify", requireAuth, (_req: Request, res: Response) => {
  res.sendStatus(200);
});

/* POST /api/login-bridge - Troca o token vindo de outro subdomínio (ex.: repo)
   por um cookie próprio do host que respondeu (ex.: academy.repo), sem precisar
   de Domain compartilhado entre subdomínios (e sem cair na restrição da PSL). */
router.post("/login-bridge", (req: Request, res: Response) => {
  const token = String(req.body?.token ?? "").trim();

  if (!token) {
    res.status(400).json({ status: "rejected", message: "Token ausente." });
    return;
  }

  try {
    jwt.verify(token, JWT_SECRET);
  } catch {
    res.status(401).json({ status: "rejected", message: "Token inválido ou expirado." });
    return;
  }

  res.cookie(COOKIE_NAME, token, COOKIE_OPTIONS);
  res.json({ status: "ok", message: "SESSÃO ESTENDIDA." });
});

export default router;
