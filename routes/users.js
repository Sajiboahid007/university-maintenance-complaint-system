const express = require("express");
const router = express.Router();
const { PrismaClient } = require("@prisma/client");
const prisma = new PrismaClient();
const bcrypt = require("bcrypt");
const SALT_ROUNDS = 10;

// CREATE - Insert new user
router.post("/insert", async (req, res) => {
  try {
    const userData = req.body;

    const hashedPassword = await bcrypt.hash(userData.Password, SALT_ROUNDS);

    const newUser = await prisma.Users.create({
      data: {
        Name: userData.Name,
        Email: userData.Email,
        Role: userData.Role,
        Phone: userData.Phone || null,
        Password: hashedPassword,
      },
    });

    res.status(201).json({
      message: "User created successfully",
      data: newUser,
    });
  } catch (error) {
    console.error("Error creating user:", error);
    res.status(500).json({
      message: "Failed to create user",
      error: error.message,
    });
  }
});

// READ - Get all users
router.get("/", async (req, res) => {
  try {
    const users = await prisma.Users.findMany({
      orderBy: { Id: "desc" },
    });

    res.status(200).json({
      message: "Users fetched successfully",
      data: users,
    });
  } catch (error) {
    console.error("Error fetching users:", error);
    res.status(500).json({
      message: "Failed to fetch users",
      error: error.message,
    });
  }
});

// READ - Get single user by ID
router.get("/get/:id", async (req, res) => {
  try {
    const userId = Number(req.params.id);

    const user = await prisma.Users.findUnique({
      where: { Id: userId },
    });

    if (!user) {
      return res.status(404).json({ message: "User not found" });
    }

    res.status(200).json({
      message: "User fetched successfully",
      data: user,
    });
  } catch (error) {
    console.error("Error fetching user:", error);
    res.status(500).json({
      message: "Failed to fetch user",
      error: error.message,
    });
  }
});

// UPDATE - Update user
router.put("/update/:id", async (req, res) => {
  try {
    const userId = Number(req.params.id);
    const updateData = req.body;

    const updatedUser = await prisma.Users.update({
      where: { Id: userId },
      data: {
        Name: updateData.Name,
        Email: updateData.Email,
        Role: updateData.Role,
        Phone: updateData.Phone || null,
      },
    });

    res.status(200).json({
      message: "User updated successfully",
      data: updatedUser,
    });
  } catch (error) {
    console.error("Error updating user:", error);
    res.status(500).json({
      message: "Failed to update user",
      error: error.message,
    });
  }
});

// DELETE - Delete user
router.delete("/delete/:id", async (req, res) => {
  try {
    const userId = Number(req.params.id);

    await prisma.Users.delete({
      where: { Id: userId },
    });

    res.status(200).json({
      message: "User deleted successfully",
    });
  } catch (error) {
    console.error("Error deleting user:", error);
    res.status(500).json({
      message: "Failed to delete user",
      error: error.message,
    });
  }
});

module.exports = router;
