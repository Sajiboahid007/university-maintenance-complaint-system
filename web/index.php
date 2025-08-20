<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>University Repair System - Login</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <style>
    body, html {
      height: 100%;
      background: linear-gradient(135deg, #4e73df, #224abe);
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
    }

    .container {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .card {
      width: 100%;
      max-width: 400px;
      border-radius: 1rem;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      background-color: #fff;
      padding: 2rem;
      transition: all 0.3s ease;
    }

    .card:hover {
      box-shadow: 0 12px 30px rgba(0,0,0,0.3);
      transform: translateY(-5px);
    }

    h2 {
      font-weight: 700;
      margin-bottom: 1.5rem;
      color: #224abe;
      text-align: center;
    }

    .btn-primary {
      background-color: #224abe;
      border: none;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #1b388a;
    }

    #login-error {
      color: #e74c3c;
      font-weight: 600;
      margin-top: 1rem;
      text-align: center;
      display: none;
    }

    #welcome-section {
      max-width: 400px;
      text-align: center;
      background: #fff;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    #welcome-section h2 {
      color: #224abe;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    #btnLogout {
      background-color: #e74c3c;
      border: none;
      font-weight: 600;
      padding: 0.5rem 2rem;
      transition: background-color 0.3s ease;
    }

    #btnLogout:hover {
      background-color: #c0392b;
    }
  </style>
</head>
<body>
  <div class="container">
    <div id="login-form" class="card shadow">
      <h2>Login</h2>
      <form id="formLogin" novalidate>
        <div class="mb-3">
          <label for="email" class="form-label fw-semibold">Email</label>
          <input
            type="email"
            id="email"
            class="form-control"
            placeholder="Enter your email"
            required
            autocomplete="email"
          />
          <div class="invalid-feedback">Please enter a valid email.</div>
        </div>
        <div class="mb-4">
          <label for="password" class="form-label fw-semibold">Password</label>
          <input
            type="password"
            id="password"
            class="form-control"
            placeholder="Enter your password"
            required
            autocomplete="current-password"
          />
          <div class="invalid-feedback">Please enter your password.</div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
      </form>
      <div id="login-error"></div>
    </div>

    <div
      id="welcome-section"
      class="shadow"
      style="display: none"
    >
      <h2>Welcome!</h2>
      <p id="welcome-msg" class="mb-4"></p>
      <button id="btnLogout" class="btn">Logout</button>
    </div>
  </div>

  <script>
    (function () {
      const formLogin = document.getElementById("formLogin");
      const loginError = document.getElementById("login-error");
      const loginFormDiv = document.getElementById("login-form");
      const welcomeSection = document.getElementById("welcome-section");
      const welcomeMsg = document.getElementById("welcome-msg");
      const btnLogout = document.getElementById("btnLogout");

      // Simple validation helper for email format
      function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      }

      formLogin.addEventListener("submit", async (e) => {
        e.preventDefault();
        loginError.style.display = "none";

        // Validate email and password
        const emailInput = document.getElementById("email");
        const passwordInput = document.getElementById("password");

        let valid = true;

        if (!emailInput.value.trim() || !isValidEmail(emailInput.value.trim())) {
          emailInput.classList.add("is-invalid");
          valid = false;
        } else {
          emailInput.classList.remove("is-invalid");
        }

        if (!passwordInput.value) {
          passwordInput.classList.add("is-invalid");
          valid = false;
        } else {
          passwordInput.classList.remove("is-invalid");
        }

        if (!valid) return;

        const email = emailInput.value.trim();
        const password = passwordInput.value;

        try {
          const res = await fetch("http://localhost:3000/api/login", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password }),
          });

          const data = await res.json();

          if (res.ok) {
                localStorage.setItem("token", data.token);
                localStorage.setItem("role", data.user.role);
                localStorage.setItem("userInfo", JSON.stringify(data.user));

                if (data.user.role === "student" || data.user.role === "Student") {
                  window.location.href = "./complaint.php";
                } else if (data.user.role === "admin") {
                  window.location.href = "admin_dashboard.php";
                } else if (data.user.role === "superAdmin") {
                  window.location.href = "superadmin_dashboard.php";
                }

            }
            // showWelcome(email);
           else {
            loginError.textContent = data.message || "Login failed";
            loginError.style.display = "block";
          }
        } catch (err) {
          loginError.textContent = "Error connecting to server";
          loginError.style.display = "block";
        }
      });

      btnLogout.addEventListener("click", () => {
        localStorage.removeItem("token");
        loginFormDiv.style.display = "block";
        welcomeSection.style.display = "none";
        loginError.style.display = "none";
        formLogin.reset();
        document.getElementById("email").classList.remove("is-invalid");
        document.getElementById("password").classList.remove("is-invalid");
      });

      function showWelcome(email) {
        loginFormDiv.style.display = "none";
        welcomeSection.style.display = "block";
        welcomeMsg.textContent = `You are logged in as ${email}.`;
      }

      // On page load, check token presence
      window.onload = () => {
        const token = localStorage.getItem("token");
        if (token) {
          // Optionally verify token with backend here before showing welcome
          showWelcome("User");
        }
      };
    })();
  </script>
</body>
</html>
