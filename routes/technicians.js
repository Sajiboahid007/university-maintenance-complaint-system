const express = require("express");
const router = express.Router();

// Example: GET /api/technicians
router.get("/", (req, res) => {
  // Logic to get technicians here...
  res.json([
    { id: 1, name: "Technician A" },
    { id: 2, name: "Technician B" },
  ]);
});

module.exports = router;
