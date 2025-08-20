const express = require("express");
const bcrypt = require("bcryptjs");
const jwt = require("jsonwebtoken");
const { PrismaClient } = require("@prisma/client");

const prisma = new PrismaClient();
const router = express.Router();

const SECRET_KEY = "THIS_IS_SECRET"; // use env variable in production

// POST /api/login
router.post("/login", async (req, res) => {
  try {
    const { email, password } = req.body;

    if (!email || !password) {
      return res.status(400).json({ message: "Email and password required" });
    }

    // Find user (case-insensitive optional)
    const user = await prisma.users.findUnique({ where: { Email: email } });
    if (!user) return res.status(401).json({ message: "Invalid credentials" });

    // Compare password
    const isMatch = await bcrypt.compare(password, user.Password);
    if (!isMatch)
      return res.status(401).json({ message: "Invalid credentials" });

    // Create JWT payload
    const payload = {
      id: user.Id,
      email: user.Email,
      role: user.Role,
      name: user.Name,
    };

    // Sign token
    const token = jwt.sign(payload, SECRET_KEY, { expiresIn: "1h" });

    // Return response
    res.json({
      message: "Login successful",
      token: "Bearer " + token, // ✅ easy to use in Authorization header
      user: payload,
    });
  } catch (err) {
    console.error("Login error:", err);
    res.status(500).json({ message: "Server error", error: err.message });
  }
});

module.exports = router;
