const express = require("express");
const router = express.Router();
const jwt = require("jsonwebtoken");
const bcrypt = require("bcryptjs");
const { PrismaClient } = require("@prisma/client");
const prisma = new PrismaClient();

const SECRET_KEY = "THIS_IS_SECRET";

// (async function () {
//   const passwordPlain = "123";
//   const hashedPassword = await bcrypt.hash(passwordPlain, 10);
//   await prisma.users.create({
//     data: {
//       Name: "Sajib",
//       Email: "abc@gmail.com",
//       Role: "student",
//       Phone: "123456789",
//       Password: hashedPassword,
//     },
//   });
//   console.log("created");
// })();

// POST /api/login
router.post("/", async (req, res) => {
  const { email, password } = req.body;
  if (!email || !password)
    return res.status(400).json({ message: "Email and password required" });

  try {
    const user = await prisma.users.findUnique({ where: { Email: email } });
    if (!user) return res.status(401).json({ message: "Invalid credentials" });

    const validPass = await bcrypt.compare(password, user.Password);
    if (!validPass)
      return res.status(401).json({ message: "Invalid credentials" });

    const payload = { id: user.Id, email: user.Email, role: user.Role };
    const token = jwt.sign(payload, SECRET_KEY, { expiresIn: "1h" });

    res.json({ token, user: payload });
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: "Server error" });
  }
});

module.exports = router;
