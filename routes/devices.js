const express = require("express");
const router = express.Router();
const { PrismaClient } = require("@prisma/client");

const prisma = new PrismaClient();

// GET all devices
router.get("/", async (req, res) => {
  try {
    const devices = await prisma.devices.findMany({
      include: { Rooms: true },
      orderBy: { Id: "desc" },
    });
    res.json({ message: "Devices fetched successfully", data: devices });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error fetching devices", error: error.message });
  }
});

// GET device by ID
router.get("/:id", async (req, res) => {
  try {
    const device = await prisma.devices.findUnique({
      where: { Id: Number(req.params.id) },
      include: { Rooms: true },
    });

    if (!device) return res.status(404).json({ message: "Device not found" });

    res.json({ message: "Device fetched successfully", data: device });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error fetching device", error: error.message });
  }
});

// CREATE device
router.post("/insert", async (req, res) => {
  try {
    const { RoomId, Type, Identifier, Status } = req.body;

    const newDevice = await prisma.devices.create({
      data: { RoomId, Type, Identifier, Status },
    });

    res
      .status(201)
      .json({ message: "Device created successfully", data: newDevice });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error creating device", error: error.message });
  }
});

// UPDATE device
router.put("/update/:id", async (req, res) => {
  try {
    const deviceId = Number(req.params.id);
    const { RoomId, Type, Identifier, Status } = req.body;

    const updatedDevice = await prisma.devices.update({
      where: { Id: deviceId },
      data: { RoomId, Type, Identifier, Status },
    });

    res.json({ message: "Device updated successfully", data: updatedDevice });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error updating device", error: error.message });
  }
});

// DELETE device
router.delete("/delete/:id", async (req, res) => {
  try {
    await prisma.devices.delete({
      where: { Id: Number(req.params.id) },
    });

    res.json({ message: "Device deleted successfully" });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error deleting device", error: error.message });
  }
});

module.exports = router;
