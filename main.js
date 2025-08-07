const { PrismaClient } = require("@prisma/client");

const express = require("express");
const cors = require("cors");
const prisma = new PrismaClient();

const app = express();
app.use(express.json());
app.use(cors());

app.listen(3000, () => {
  console.log("server running on 3000");
});

app.post("/user/insert", async (req, res) => {
  try {
    const userReq = req?.body;
    const userList = await prisma.Users.create({
      data: {
        Name: userReq?.Name,
        Email: userReq?.Email,
        Role: userReq?.Role,
        Phone: userReq?.Phone,
        Password: userReq?.Password,
      },
    });
    return res.status(201).json({
      message: "Successfully Created",
      data: userList,
    });
  } catch (error) {
    res.status(500).json({
      message: "Something is wrong!",
      error: error,
      message,
    });
  }
});

app.get("/user/get", async (req, res) => {
  try {
    const usersList = await prisma.Users.findMany({
      orderBy: { Id: "desc" },
    });
    return res.status(201).json({
      message: "successfully get User Info",
      data: usersList,
    });
  } catch (error) {
    console.log("Something is error", error);

    res.status(500).json({
      message: "Something is wrong!",
    });
  }
});

app.get("/user/get/:id", async (req, res) => {
  try {
    const userId = Number(req.params.id);
    const user = await prisma.Users.findUnique({
      where: { Id: userId },
    });

    if (!user) {
      return res.status(404).json({ message: "User not found" });
    }

    res.status(200).json({
      message: "User fetched successfully",
      data: user,
    });
  } catch (error) {
    console.error("Error:", error);
    res.status(500).json({
      message: "Failed to fetch user",
      error: error.message,
    });
  }
});

app.put("/user/update/:id", async (req, res) => {
  try {
    const userId = Number(req.params.id);
    const userData = req.body;

    const updatedUser = await prisma.Users.update({
      where: { Id: userId },
      data: {
        Name: userData.Name,
        Email: userData.Email,
        Role: userData.Role,
        Phone: userData.Phone,
        // Password: userData.Password, // ⚠️ Only include if updating password
      },
    });

    res.status(200).json({
      message: "User updated successfully",
      data: updatedUser,
    });
  } catch (error) {
    console.error("Error:", error);
    res.status(500).json({
      message: "Failed to update user",
      error: error.message,
    });
  }
});

app.delete("/user/delete/:id", async (req, res) => {
  try {
    const userId = Number(req.params.id);

    await prisma.Users.delete({
      where: { Id: userId },
    });

    res.status(200).json({
      message: "User deleted successfully",
    });
  } catch (error) {
    console.error("Error:", error);
    res.status(500).json({
      message: "Failed to delete user",
      error: error.message,
    });
  }
});

app.post("/unit/insert", async (req, res) => {
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

app.get("/unit/get", async (req, res) => {
  try {
    const units = await prisma.Units.findMany({
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

app.get("/unit/get/:id", async (req, res) => {
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

app.put("/unit/update/:id", async (req, res) => {
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

app.delete("/unit/delete/:id", async (req, res) => {
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
