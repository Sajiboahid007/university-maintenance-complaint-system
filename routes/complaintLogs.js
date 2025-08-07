const express = require("express");
const router = express.Router();
const { PrismaClient } = require("@prisma/client");

const prisma = new PrismaClient();

// GET all complaint logs
router.get("/", async (req, res) => {
  try {
    const logs = await prisma.complaintLogs.findMany({
      include: { Complaints: true },
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
router.get("/:id", async (req, res) => {
  try {
    const log = await prisma.complaintLogs.findUnique({
      where: { Id: Number(req.params.id) },
      include: { Complaints: true },
    });
    if (!log)
      return res.status(404).json({ message: "Complaint log not found" });
    res.json({ message: "Complaint log fetched successfully", data: log });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error fetching complaint log", error: error.message });
  }
});

// CREATE complaint log
router.post("/insert", async (req, res) => {
  try {
    const { ComplaintId, Action, ActionDate } = req.body;

    const newLog = await prisma.complaintLogs.create({
      data: {
        ComplaintId,
        Action,
        ActionDate: ActionDate ? new Date(ActionDate) : undefined,
      },
    });

    res
      .status(201)
      .json({ message: "Complaint log created successfully", data: newLog });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error creating complaint log", error: error.message });
  }
});

// UPDATE complaint log
router.put("/update/:id", async (req, res) => {
  try {
    const logId = Number(req.params.id);
    const { ComplaintId, Action, ActionDate } = req.body;

    const updatedLog = await prisma.complaintLogs.update({
      where: { Id: logId },
      data: {
        ComplaintId,
        Action,
        ActionDate: ActionDate ? new Date(ActionDate) : undefined,
      },
    });

    res.json({
      message: "Complaint log updated successfully",
      data: updatedLog,
    });
  } catch (error) {
    res
      .status(500)
      .json({ message: "Error updating complaint log", error: error.message });
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
