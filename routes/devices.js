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
router.get("/get/:id", async (req, res) => {
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

    // Validate required fields
    if (!RoomId || !Type || !Status) {
      return res.status(400).json({
        message: "Missing required fields",
        required: ["RoomId", "Type", "Status"],
      });
    }

    const newDevice = await prisma.devices.create({
      data: {
        Rooms: {
          connect: {
            Id: Number(RoomId),
          },
        },
        Type,
        Identifier: Identifier || null, // Handle optional field
        Status,
      },
      include: {
        Rooms: true, // Include the related room data in the response
      },
    });

    res.status(201).json({
      message: "Device created successfully",
      data: newDevice,
    });
  } catch (error) {
    console.error("Error creating device:", error);
    res.status(500).json({
      message: "Error creating device",
      error: error.message,
      details: {
        prismaCode: error.code,
        meta: error.meta,
      },
    });
  }
});

// UPDATE device
router.put("/update/:id", async (req, res) => {
  try {
    const deviceId = Number(req.params.id);
    const { RoomId, Type, Identifier, Status } = req.body;

    const updatedDevice = await prisma.devices.update({
      where: {
        Id: deviceId,
      },
      data: {
        Rooms: {
          connect: {
            Id: Number(RoomId),
          },
        },
        Type,
        Identifier,
        Status,
      },
      include: {
        Rooms: true, // Include the related room data in the response
      },
    });

    res.json({
      message: "Device updated successfully",
      data: updatedDevice,
    });
  } catch (error) {
    console.error("Error updating device:", error);
    res.status(500).json({
      message: "Error updating device",
      error: error.message,
      details: error.meta, // Include Prisma error details if available
    });
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
