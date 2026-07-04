import express, { type Request, type Response, type NextFunction } from "express";
import cors from "cors";
import cookieParser from "cookie-parser";
import challenges from "./servidor/routes/challanges.ts";
import authRoutes from "./routes/auth";

const app = express();
const PORT = Number(process.env.PORT ?? 3000);

/* Origens de onde o Nginx serve o front (login.js usa credentials: "include";
academy.repo precisa estar aqui também pra falar com /api/login-bridge) */
const FRONTEND_ORIGINS = ["http://repo", "http://academy.repo"];

/* Middlewares globais. Rodam em toda request. Controla se vai adiante (next) ou
se lança exceção e encerra */
app.use(cors({ origin: FRONTEND_ORIGINS, credentials: true }));
app.use(express.json());
app.use(cookieParser());
 app.use((req: Request, _res: Response, next: NextFunction) => {
  // console.log(`${req.method} ${req.url}`);
  next();
});
 
/* ROTAS */
 
// health endpoint
app.get("/api/health", (_req: Request, res: Response) => {
  res.json({ status: "up" });
});
 
// Endpoint para todas as rotas presentes em challenges
app.use("/api", challenges);

// Rotas de autenticação (login, logout, verify)
app.use("/api", authRoutes);


/* 404 e tratamento/captura de erros */ 
app.use((_req: Request, res: Response) => {
  res.status(404).json({ error: "Rota não encontrada" });
});
 
app.use((err: unknown, _req: Request, res: Response, _next: NextFunction) => {
  // console.error(err);
  res.status(500).json({ error: "Erro interno" });
});
 
app.listen(PORT, () => console.log(`backend em http://repo:${PORT}`));
