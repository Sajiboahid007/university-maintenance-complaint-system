const express = require("express");
const router = express.Router();
const { PrismaClient } = require("@prisma/client");
const prisma = new PrismaClient();

// GET all technicians
router.get("/", async (req, res) => {
  try {
    const technicians = await prisma.Technicians.findMany({
      orderBy: { Id: "desc" },
    });
    res.json({
      message: "Technicians fetched successfully",
      data: technicians,
    });
  } catch (error) {
    console.error(error);
    res
      .status(500)
      .json({ message: "Failed to fetch technicians", error: error.message });
  }
});

// GET one technician by id
router.get("/get/:id", async (req, res) => {
  const id = Number(req.params.id);
  try {
    const technician = await prisma.Technicians.findUnique({
      where: { Id: id },
    });
    if (!technician)
      return res.status(404).json({ message: "Technician not found" });
    res.json({ message: "Technician fetched successfully", data: technician });
  } catch (error) {
    console.error(error);
    res
      .status(500)
      .json({ message: "Failed to fetch technician", error: error.message });
  }
});

// POST insert new technician
router.post("/insert", async (req, res) => {
  const { Name, Phone, AssignedArea } = req.body;
  if (!Name) return res.status(400).json({ message: "Name is required" });

  try {
    const newTechnician = await prisma.Technicians.create({
      data: { Name, Phone, AssignedArea },
    });
    res.status(201).json({
      message: "Technician created successfully",
      data: newTechnician,
    });
  } catch (error) {
    console.error(error);
    res
      .status(500)
      .json({ message: "Failed to create technician", error: error.message });
  }
});

// PUT update technician by id
router.put("/update/:id", async (req, res) => {
  const id = Number(req.params.id);
  const { Name, Phone, AssignedArea } = req.body;
  if (!Name) return res.status(400).json({ message: "Name is required" });

  try {
    const updatedTechnician = await prisma.Technicians.update({
      where: { Id: id },
      data: { Name, Phone, AssignedArea },
    });
    res.json({
      message: "Technician updated successfully",
      data: updatedTechnician,
    });
  } catch (error) {
    console.error(error);
    res
      .status(500)
      .json({ message: "Failed to update technician", error: error.message });
  }
});

// DELETE technician by id
router.delete("/delete/:id", async (req, res) => {
  const id = Number(req.params.id);
  try {
    await prisma.Technicians.delete({
      where: { Id: id },
    });
    res.json({ message: "Technician deleted successfully" });
  } catch (error) {
    console.error(error);
    res
      .status(500)
      .json({ message: "Failed to delete technician", error: error.message });
  }
});

module.exports = router;
