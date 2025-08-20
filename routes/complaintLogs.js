const express = require("express");
const router = express.Router();
const { PrismaClient } = require("@prisma/client");

const prisma = new PrismaClient();

// GET all complaint logs
router.get("/", async (req, res) => {
  try {
    const logs = await prisma.complaintLogs.findMany({
      include: {
        Complaints: {
          include: {
            Devices: {
              include: {
                Rooms: {
                  include: { Units: true },
                },
              },
            },
            Users: true,
          },
        },
      },
      orderBy: { Id: "desc" },
    });
    res.json({ message: "Complaint logs fetched successfully", data: logs });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error fetching complaint logs", error: error.message });
  }
});

// GET complaint log by ID
router.get("/", async (req, res) => {
  try {
    const complaints = await prisma.complaints.findMany({
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
      },
      orderBy: { CreatedAt: "desc" },
    });

    res.json({ message: "Complaints fetched successfully", data: complaints });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error fetching complaints", error: error.message });
  }
});

// GET single complaint by ID
router.get("/get/:id", async (req, res) => {
  try {
    const complaint = await prisma.complaints.findUnique({
      where: { Id: Number(req.params.id) },
      include: {
        Users: true,
        Devices: {
          include: {
            Rooms: {
              include: { Units: true },
            },
          },
        },
      },
    });

    if (!complaint)
      return res.status(404).json({ message: "Complaint not found" });
    res.json({ message: "Complaint fetched successfully", data: complaint });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error fetching complaint", error: error.message });
  }
});

// UPDATE complaint status only
router.put("/update/:id", async (req, res) => {
  try {
    const complaintId = Number(req.params.id);
    const { Status } = req.body;

    if (!Status) return res.status(400).json({ message: "Status is required" });

    const updatedComplaint = await prisma.complaints.update({
      where: { Id: complaintId },
      data: { Status },
    });

    res.json({
      message: "Complaint status updated successfully",
      data: updatedComplaint,
    });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error updating complaint", error: error.message });
  }
});

// DELETE complaint log
router.delete("/delete/:id", async (req, res) => {
  try {
    await prisma.complaintLogs.delete({
      where: { Id: Number(req.params.id) },
    });
    res.json({ message: "Complaint log deleted successfully" });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error deleting complaint log", error: error.message });
  }
});

module.exports = router;
