const express = require("express");
const router = express.Router();
const { PrismaClient } = require("@prisma/client");

const prisma = new PrismaClient();

// Get all rooms
router.get("/", async (req, res) => {
  try {
    const rooms = await prisma.rooms.findMany({
      include: {
        Units: true,
      },
      orderBy: {
        Id: "desc",
      },
    });
    res.json({ message: "Rooms fetched successfully", data: rooms });
  } catch (error) {
    res.status(500).json({ message: "Error fetching rooms", error });
  }
});

// Get a room by ID
router.get("/:id", async (req, res) => {
  try {
    const room = await prisma.rooms.findUnique({
      where: { Id: parseInt(req.params.id) },
      include: { Units: true },
    });

    if (!room) return res.status(404).json({ message: "Room not found" });

    res.json({ message: "Room fetched successfully", data: room });
  } catch (error) {
    res.status(500).json({ message: "Error fetching room", error });
  }
});

// Create a new room
router.post("/insert", async (req, res) => {
  try {
    const { UnitId, RoomNo } = req.body;

    const newRoom = await prisma.rooms.create({
      data: { UnitId, RoomNo },
    });

    res
      .status(201)
      .json({ message: "Room created successfully", data: newRoom });
  } catch (error) {
    res.status(500).json({ message: "Error creating room", error });
  }
});

// Update a room
router.put("/update/:id", async (req, res) => {
  try {
    const { UnitId, RoomNo } = req.body;

    const updatedRoom = await prisma.rooms.update({
      where: { Id: parseInt(req.params.id) },
      data: { UnitId, RoomNo },
    });

    res.json({ message: "Room updated successfully", data: updatedRoom });
  } catch (error) {
    res.status(500).json({ message: "Error updating room", error });
  }
});

// Delete a room
router.delete("/delete/:id", async (req, res) => {
  try {
    await prisma.rooms.delete({
      where: { Id: parseInt(req.params.id) },
    });

    res.json({ message: "Room deleted successfully" });
  } catch (error) {
    res.status(500).json({ message: "Error deleting room", error });
  }
});

module.exports = router;
