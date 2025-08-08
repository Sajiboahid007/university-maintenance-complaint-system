const express = require("express");
const router = express.Router();
const { PrismaClient } = require("@prisma/client");
const prisma = new PrismaClient();

router.post("/insert", async (req, res) => {
  try {
    const unitData = req.body;

    const newUnit = await prisma.Units.create({
      data: {
        LevelId: Number(unitData.LevelId),
        Name: unitData.Name,
      },
    });

    return res.status(201).json({
      message: "Unit created successfully",
      data: newUnit,
    });
  } catch (error) {
    console.error("Error:", error);

    res.status(500).json({
      message: "Failed to create unit",
      error: error.message,
    });
  }
});

router.get("/", async (req, res) => {
  try {
    const units = await prisma.Units.findMany({
      include: { Levels: true },
      orderBy: { Id: "desc" },
    });
    res.status(200).json({
      message: "Units fetched successfully",
      data: units,
    });
  } catch (error) {
    console.error("Error:", error);
    res.status(500).json({
      message: "Failed to fetch units",
      error: error.message,
    });
  }
});

router.get("/get/:id", async (req, res) => {
  try {
    const unitId = Number(req.params.id);
    const unit = await prisma.Units.findUnique({
      where: { Id: unitId },
    });

    if (!unit) {
      return res.status(404).json({ message: "Unit not found" });
    }

    res.status(200).json({
      message: "Unit fetched successfully",
      data: unit,
    });
  } catch (error) {
    console.error("Error:", error);
    res.status(500).json({
      message: "Failed to fetch unit",
      error: error.message,
    });
  }
});

router.put("/update/:id", async (req, res) => {
  try {
    const unitId = Number(req.params.id);
    const updateData = req.body;

    const updatedUnit = await prisma.Units.update({
      where: { Id: unitId },
      data: {
        Name: updateData.Name,
        LevelId: updateData.LevelId,
      },
    });

    res.status(200).json({
      message: "Unit updated successfully",
      data: updatedUnit,
    });
  } catch (error) {
    console.error("Error:", error);

    res.status(500).json({
      message: "Failed to update unit",
      error: error.message,
    });
  }
});

router.delete("/delete/:id", async (req, res) => {
  try {
    const unitId = parseInt(req.params.id);

    await prisma.Units.delete({
      where: { Id: unitId },
    });

    res.status(200).json({
      message: "Unit deleted successfully",
    });
  } catch (error) {
    console.error("Error:", error);

    res.status(500).json({
      message: "Failed to delete unit",
      error: error.message,
    });
  }
});

module.exports = router;
