import { PrismaClient } from "@prisma/client";
import bcrypt from "bcrypt";

const prisma = new PrismaClient();

async function main() {
  const existing = await prisma.user.findUnique({ where: { username: "admin" } });
  if (!existing) {
    const passwordHash = await bcrypt.hash("admin123", 10);
    await prisma.user.create({
      data: {
        username: "admin",
        email: "admin@apreder4.local",
        passwordHash,
        role: "admin",
        approved: true,
      },
    });
    console.log("Admin user created");
  } else {
    console.log("Admin user already exists");
  }
}

main()
  .catch(console.error)
  .finally(() => prisma.$disconnect());
