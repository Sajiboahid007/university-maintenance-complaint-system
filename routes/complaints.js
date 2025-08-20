const express = require("express");
const router = express.Router();
const { PrismaClient } = require("@prisma/client");

const prisma = new PrismaClient();

// GET all complaints
router.get("/", async (req, res) => {
  try {
    const complaints = await prisma.complaints.findMany({
      include: {
        Users: true,
        ComplaintLogs: true,
        Devices: {
          include: {
            Rooms: {
              include: {
                Units: true,
              },
            },
          },
        },
      },
      orderBy: { Id: "desc" },
    });

    res.json({ message: "Complaints fetched successfully", data: complaints });
  } catch (error) {
    res.status(500).json({
      message: "Error fetching complaints",
      error: error.message,
    });
  }
});

// GET complaint by ID
router.get("/get/:id", async (req, res) => {
  try {
    const complaint = await prisma.complaints.findUnique({
      where: { Id: Number(req.params.id) },
      include: {
        Users: true,
        ComplaintLogs: true,
        Devices: {
          include: {
            Rooms: {
              include: {
                Units: true,
              },
            },
          },
        },
      },
    });

    if (!complaint) {
      return res.status(404).json({ message: "Complaint not found" });
    }

    res.json({ message: "Complaint fetched successfully", data: complaint });
  } catch (error) {
    res.status(500).json({
      message: "Error fetching complaint",
      error: error.message,
    });
  }
});

// CREATE complaint
router.post("/insert", async (req, res) => {
  try {
    const { UserId, DeviceId, Description, Status } = req.body;

    // Validate UserId
    const userExists = await prisma.Users.findUnique({
      where: { Id: Number(UserId) },
    });
    if (!userExists) return res.status(400).json({ message: "User not found" });

    // Validate DeviceId
    const deviceExists = await prisma.Devices.findUnique({
      where: { Id: Number(DeviceId) },
    });
    if (!deviceExists)
      return res.status(400).json({ message: "Device not found" });

    const newComplaint = await prisma.complaints.create({
      data: {
        UserId: Number(UserId),
        DeviceId: Number(DeviceId),
        Description,
        Status: Status || "pending",
      },
      include: {
        Users: true,
        Devices: {
          include: {
            Rooms: { include: { Units: true } },
          },
        },
        ComplaintLogs: true,
      },
    });

    res.status(201).json({
      message: "Complaint created successfully",
      data: newComplaint,
    });
  } catch (error) {
    res.status(500).json({
      message: "Error creating complaint",
      error: error.message,
    });
  }
});

// UPDATE complaint
router.put("/update/:id", async (req, res) => {
  try {
    const complaintId = Number(req.params.id);
    const { UserId, DeviceId, Description, Status } = req.body;

    const updatedComplaint = await prisma.complaints.update({
      where: { Id: complaintId },
      data: {
        UserId: Number(UserId),
        DeviceId: Number(DeviceId),
        Description,
        Status,
      },
      include: {
        Users: true,
        Devices: {
          include: {
            Rooms: {
              include: {
                Units: true,
              },
            },
          },
        },
        ComplaintLogs: true,
      },
    });

    res.json({
      message: "Complaint updated successfully",
      data: updatedComplaint,
    });
  } catch (error) {
    res.status(500).json({
      message: "Error updating complaint",
      error: error.message,
    });
  }
});

// DELETE complaint
router.delete("/delete/:id", async (req, res) => {
  try {
    await prisma.complaints.delete({
      where: { Id: Number(req.params.id) },
    });

    res.json({ message: "Complaint deleted successfully" });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error deleting complaint", error: error.message });
  }
});

module.exports = router;
