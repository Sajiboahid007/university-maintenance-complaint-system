const express = require("express");
const cors = require("cors");

const app = express();
const PORT = 3000;

app.use(cors());
app.use(express.json());

// Import routes
const loginRouter = require("./routes/login");
const techniciansRouter = require("./routes/technicians");
const unitRouter = require("./routes/unit");
const levelsRouter = require("./routes/levels");
const roomsRouter = require("./routes/rooms");
const devicesRouter = require("./routes/devices");
const complaintsRouter = require("./routes/complaints");
const complaintLogsRouter = require("./routes/complaintLogs");

// Mount routes under /api/ prefix
app.use("/api/login", loginRouter);
app.use("/api/technicians", techniciansRouter);
app.use("/api/unit", unitRouter);
app.use("/api/levels", levelsRouter);
app.use("/api/rooms", roomsRouter);
app.use("/api/devices", devicesRouter);
app.use("/api/complaints", complaintsRouter);
app.use("/api/complaintLogs", complaintLogsRouter);

// Default route for sanity check
app.get("/", (req, res) => {
  res.send("API is running");
});

app.listen(PORT, () => {
  console.log(`Server running at http://localhost:${PORT}`);
});
