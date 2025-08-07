const express = require("express");
const router = express.Router();
const { PrismaClient } = require("@prisma/client");
const prisma = new PrismaClient();

// CREATE: Insert a new level
router.post("/insert", async (req, res) => {
  try {
    const { Name } = req.body;

    const newLevel = await prisma.Levels.create({
      data: { Name },
    });

    res.status(201).json({
      message: "Level created successfully",
      data: newLevel,
    });
  } catch (error) {
    console.error("Error creating level:", error);
    res.status(500).json({
      message: "Failed to create level",
      error: error.message,
    });
  }
});

// READ ALL: Get all levels
router.get("/", async (req, res) => {
  try {
    const levels = await prisma.Levels.findMany({
      orderBy: { Id: "desc" },
    });

    res.status(200).json({
      message: "Levels fetched successfully",
      data: levels,
    });
  } catch (error) {
    console.error("Error fetching levels:", error);
    res.status(500).json({
      message: "Failed to fetch levels",
      error: error.message,
    });
  }
});

// READ ONE: Get level by ID
router.get("/get/:id", async (req, res) => {
  try {
    const levelId = Number(req.params.id);

    const level = await prisma.Levels.findUnique({
      where: { Id: levelId },
    });

    if (!level) {
      return res.status(404).json({ message: "Level not found" });
    }

    res.status(200).json({
      message: "Level fetched successfully",
      data: level,
    });
  } catch (error) {
    console.error("Error fetching level:", error);
    res.status(500).json({
      message: "Failed to fetch level",
      error: error.message,
    });
  }
});

// UPDATE: Update level by ID
router.put("/update/:id", async (req, res) => {
  try {
    const levelId = Number(req.params.id);
    const { Name } = req.body;

    const updatedLevel = await prisma.Levels.update({
      where: { Id: levelId },
      data: { Name },
    });

    res.status(200).json({
      message: "Level updated successfully",
      data: updatedLevel,
    });
  } catch (error) {
    console.error("Error updating level:", error);
    res.status(500).json({
      message: "Failed to update level",
      error: error.message,
    });
  }
});

// DELETE: Delete level by ID
router.delete("/delete/:id", async (req, res) => {
  try {
    const levelId = Number(req.params.id);

    await prisma.Levels.delete({
      where: { Id: levelId },
    });

    res.status(200).json({
      message: "Level deleted successfully",
    });
  } catch (error) {
    console.error("Error deleting level:", error);
    res.status(500).json({
      message: "Failed to delete level",
      error: error.message,
    });
  }
});

module.exports = router;
