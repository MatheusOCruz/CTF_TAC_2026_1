import express from "express";
import path from "path";
import cors from "cors";
import { PrismaClient } from "@prisma/client";
import authRoutes from "./auth/routes";
import userRoutes from "./users/routes";
import adminRoutes from "./admin/routes";
import submissionRoutes from "./submissions/routes";

const app = express();
const prisma = new PrismaClient();

app.use(cors());
app.use(express.json());

app.use("/auth", authRoutes);
app.use("/user", userRoutes);
app.use("/admin", adminRoutes);
app.use("/submission", submissionRoutes);

app.get("/health", async (_req, res) => {
  try {
    await prisma.$queryRaw`SELECT 1`;
    res.json({ status: "ok", db: "connected" });
  } catch {
    res.status(503).json({ status: "error", db: "disconnected" });
  }
});

app.use(express.static(path.join(__dirname, "..", "public")));

app.get("*", (_req, res) => {
  res.sendFile(path.join(__dirname, "..", "public", "index.html"));
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Apreder4 running on port ${PORT}`);
});
