(function () {
  const root = document.documentElement;
  const storageKey = "city-events-theme";
  const button = document.getElementById("themeToggle");

  const storedTheme = localStorage.getItem(storageKey);
  const theme = storedTheme || "dark";

  root.setAttribute("data-bs-theme", theme);

  // admin auth local storage sync 
  const authAction = document.body
    ? document.body.getAttribute("data-admin-auth-action")
    : "";
  const authRedirect = document.body
    ? document.body.getAttribute("data-admin-auth-redirect")
    : "";
  const authUsername = document.body
    ? document.body.getAttribute("data-admin-auth-username") || "Admin"
    : "Admin";

  if (authAction === "set") {
    try {
      localStorage.setItem(
        "city-events-admin-auth",
        JSON.stringify({
          loggedIn: true,
          username: authUsername,
          loginAt: Date.now(),
        }),
      );
    } catch (e) {
      console.error("Failed to persist admin auth state", e);
    }

    if (authRedirect) {
      window.location.replace(authRedirect);
      return;
    }
  }

  if (authAction === "clear") {
    try {
      localStorage.removeItem("city-events-admin-auth");
    } catch (e) {
      console.error("Failed to clear admin auth state", e);
    }

    if (authRedirect) {
      window.location.replace(authRedirect);
      return;
    }
  }

  // sync public navbar auth button from stored admin state.
  const navbarAuthButton = document.getElementById("navbarAuthButton");
  if (navbarAuthButton) {
    let isStoredLoggedIn = false;
    try {
      const rawAuth = localStorage.getItem("city-events-admin-auth");
      if (rawAuth) {
        const parsedAuth = JSON.parse(rawAuth);
        isStoredLoggedIn = Boolean(parsedAuth && parsedAuth.loggedIn);
      }
    } catch (e) {
      isStoredLoggedIn = false;
    }

    const loginLabel =
      navbarAuthButton.getAttribute("data-login-label") || "Login";
    const dashboardLabel =
      navbarAuthButton.getAttribute("data-dashboard-label") || "Dashboard";
    const loginHref =
      navbarAuthButton.getAttribute("data-login-href") || "admin/login.php";
    const dashboardHref =
      navbarAuthButton.getAttribute("data-dashboard-href") ||
      "admin/dashboard.php";

    if (isStoredLoggedIn) {
      navbarAuthButton.textContent = dashboardLabel;
      navbarAuthButton.setAttribute("href", dashboardHref);
    } else {
      navbarAuthButton.textContent = loginLabel;
      navbarAuthButton.setAttribute("href", loginHref);
    }
  }

  // theme toggle button
  if (button) {
    button.textContent = theme === "dark" ? "Light" : "Dark";

    button.addEventListener("click", function () {
      const current = root.getAttribute("data-bs-theme") || "light";
      const next = current === "dark" ? "light" : "dark";
      root.setAttribute("data-bs-theme", next);
      localStorage.setItem(storageKey, next);
      button.textContent = next === "dark" ? "Light" : "Dark";
    });
  }

  // scroll to top button behavior
  const scrollTopButton = document.getElementById("scrollTopButton");
  if (scrollTopButton) {
    const toggleScrollTopVisibility = function () {
      const shouldShow = window.scrollY > 300;
      scrollTopButton.classList.toggle("is-visible", shouldShow);
    };

    window.addEventListener("scroll", toggleScrollTopVisibility, {
      passive: true,
    });

    scrollTopButton.addEventListener("click", function () {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });

    toggleScrollTopVisibility();
  }

  // event search and filter functionality
  const searchInput = document.getElementById("eventsSearch");
  const categoryInput = document.getElementById("eventsCategory");
  const dateInput = document.getElementById("eventsDate");
  const resetButton = document.getElementById("eventsReset");
  const emptyState = document.getElementById("eventsEmptyState");
  const countLabel = document.getElementById("eventCountLabel");
  const eventCards = document.querySelectorAll("[data-event-card]");

  if (
    searchInput &&
    categoryInput &&
    dateInput &&
    resetButton &&
    eventCards.length > 0
  ) {
    const applyFilters = function () {
      const searchText = searchInput.value.trim().toLowerCase();
      const category = categoryInput.value.trim().toLowerCase();
      const dateValue = dateInput.value;

      let visibleCount = 0;

      // filter each event card based on search, category, and date
      eventCards.forEach(function (card) {
        const title = card.getAttribute("data-title") || "";
        const location = card.getAttribute("data-location") || "";
        const cardCategory = card.getAttribute("data-category") || "";
        const cardDate = card.getAttribute("data-date") || "";

        const matchesSearch =
          searchText === "" ||
          title.includes(searchText) ||
          location.includes(searchText);
        const matchesCategory = category === "" || cardCategory === category;
        const matchesDate = dateValue === "" || cardDate === dateValue;

        const isVisible = matchesSearch && matchesCategory && matchesDate;
        card.classList.toggle("d-none", !isVisible);

        if (isVisible) {
          visibleCount += 1;
        }
      });

      // show/hide empty state
      if (emptyState) {
        emptyState.classList.toggle("d-none", visibleCount !== 0);
      }

      // update count label
      if (countLabel) {
        countLabel.textContent =
          visibleCount + " event" + (visibleCount === 1 ? "" : "s");
      }
    };

    searchInput.addEventListener("input", applyFilters);
    categoryInput.addEventListener("change", applyFilters);
    dateInput.addEventListener("change", applyFilters);

    resetButton.addEventListener("click", function () {
      searchInput.value = "";
      categoryInput.value = "";
      dateInput.value = "";
      applyFilters();
    });

    applyFilters();
  }

  // copy to clipboard function for event sharing
  window.copyToClipboard = function (text) {
    navigator.clipboard.writeText(text).then(
      function () {
        alert("Link copied to clipboard");
      },
      function (err) {
        console.error("Failed to copy because ", err);
      },
    );
  };

  // login page enhancements
  const loginForms = document.querySelectorAll(".login-form");
  const loginInputs = document.querySelectorAll(".login-input");
  const authToggleButtons = document.querySelectorAll("[data-auth-target]");
  const authForms = document.querySelectorAll("[data-auth-form]");
  const authTitle = document.getElementById("authTitle");
  const authSubtitle = document.getElementById("authSubtitle");

  if (authToggleButtons.length > 0 && authForms.length > 0) {
    const copyByMode = {
      login: {
        title: "Welcome Back",
        subtitle: "Sign in to manage your events",
      },
      signup: {
        title: "Create Account",
        subtitle: "Sign up to manage your events",
      },
    };

    const setAuthMode = function (mode) {
      const currentMode = mode === "signup" ? "signup" : "login";

      authToggleButtons.forEach(function (button) {
        const isActive = button.getAttribute("data-auth-target") === currentMode;
        button.classList.toggle("active", isActive);
      });

      authForms.forEach(function (form) {
        const isTargetForm = form.getAttribute("data-auth-form") === currentMode;
        form.classList.toggle("d-none", !isTargetForm);
      });

      if (authTitle) {
        authTitle.textContent = copyByMode[currentMode].title;
      }

      if (authSubtitle) {
        authSubtitle.textContent = copyByMode[currentMode].subtitle;
      }

      const activeForm = document.querySelector(
        '.auth-form[data-auth-form="' + currentMode + '"]',
      );
      const firstField = activeForm
        ? activeForm.querySelector("input:not([type='hidden'])")
        : null;
      if (firstField) {
        firstField.focus();
      }
    };

    authToggleButtons.forEach(function (button) {
      button.addEventListener("click", function () {
        setAuthMode(button.getAttribute("data-auth-target") || "login");
      });
    });

    const activeToggle = document.querySelector(".auth-toggle-btn.active");
    if (activeToggle) {
      setAuthMode(activeToggle.getAttribute("data-auth-target") || "login");
    }
  }

  if (loginForms.length > 0 && loginInputs.length > 0) {
    // auto-focus first input on page load
    loginInputs[0].focus();

    // add input validation feedback
    loginInputs.forEach(function (input) {
      input.addEventListener("blur", function () {
        if (this.value.trim() === "") {
          this.classList.add("is-invalid");
        } else {
          this.classList.remove("is-invalid");
        }
      });

      // remove invalid class on focus
      input.addEventListener("focus", function () {
        this.classList.remove("is-invalid");
      });
    });

    // form submission enhancement
    loginForms.forEach(function (form) {
      form.addEventListener("submit", function (e) {
        let isValid = true;
        const formInputs = form.querySelectorAll(".login-input");

        formInputs.forEach(function (input) {
          if (input.value.trim() === "") {
            input.classList.add("is-invalid");
            isValid = false;
          } else {
            input.classList.remove("is-invalid");
          }
        });

        const signupPassword = form.querySelector("#signup_password");
        const signupConfirm = form.querySelector("#signup_confirm_password");
        if (signupPassword && signupConfirm) {
          if (
            signupConfirm.value.trim() !== "" &&
            signupPassword.value !== signupConfirm.value
          ) {
            signupConfirm.classList.add("is-invalid");
            isValid = false;
          }
        }

        if (!isValid) {
          e.preventDefault();
        }
      });
    });
  }

  // Contact form validation
  const contactForm = document.getElementById("contactForm");

  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      let isValid = true;
      const form = e.target;

      // Clear previous validation states
      form.querySelectorAll(".is-invalid").forEach(function (element) {
        element.classList.remove("is-invalid");
      });

      // Validate name
      const nameInput = form.querySelector("#name");
      if (nameInput && nameInput.value.trim() === "") {
        nameInput.classList.add("is-invalid");
        isValid = false;
      }

      // Validate email
      const emailInput = form.querySelector("#email");
      if (emailInput) {
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email === "") {
          emailInput.classList.add("is-invalid");
          isValid = false;
        } else if (!emailRegex.test(email)) {
          emailInput.classList.add("is-invalid");
          isValid = false;
        }
      }

      // Validate message
      const messageInput = form.querySelector("#message");
      if (messageInput && messageInput.value.trim() === "") {
        messageInput.classList.add("is-invalid");
        isValid = false;
      }

      // Prevent form submission if validation fails
      if (!isValid) {
        e.preventDefault();

        // Scroll to first invalid field
        const firstInvalid = form.querySelector(".is-invalid");
        if (firstInvalid) {
          firstInvalid.scrollIntoView({ behavior: "smooth", block: "center" });
          firstInvalid.focus();
        }
      }
    });

    // real-time validation feedback
    const inputs = contactForm.querySelectorAll("input, textarea");
    inputs.forEach(function (input) {
      input.addEventListener("blur", function () {
        validateField(this);
      });

      input.addEventListener("input", function () {
        // remove invalid class on input
        if (this.classList.contains("is-invalid")) {
          validateField(this);
        }
      });
    });

    function validateField(field) {
      field.classList.remove("is-invalid");

      if (field.hasAttribute("required") && field.value.trim() === "") {
        field.classList.add("is-invalid");
        return false;
      }

      if (field.type === "email") {
        const email = field.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email !== "" && !emailRegex.test(email)) {
          field.classList.add("is-invalid");
          return false;
        }
      }

      return true;
    }
  }

  // Dashboard enhancements
  const dashboardBody = document.querySelector(".dashboard-body");
  const eventRows = document.querySelectorAll(".event-row");
  const actionButtons = document.querySelectorAll(".action-btn");

	if (dashboardBody) {
    // Mobile sidebar toggle for admin pages
    const dashboardSidebar = document.querySelector(".dashboard-sidebar");
    const sidebarToggles = document.querySelectorAll("[data-sidebar-toggle]");
    const mobileBreakpoint = window.matchMedia("(max-width: 768px)");

    const setSidebarState = function (isOpen) {
      dashboardBody.classList.toggle("sidebar-open", isOpen);

      sidebarToggles.forEach(function (toggle) {
        toggle.setAttribute("aria-expanded", String(isOpen));
      });
    };

    if (dashboardSidebar && sidebarToggles.length > 0) {
      // Start with sidebar hidden on small screens.
      if (mobileBreakpoint.matches) {
        setSidebarState(false);
      }

      sidebarToggles.forEach(function (toggle) {
        toggle.addEventListener("click", function () {
          if (!mobileBreakpoint.matches) {
            return;
          }
          const shouldOpen = !dashboardBody.classList.contains("sidebar-open");
          setSidebarState(shouldOpen);
        });
      });

      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && mobileBreakpoint.matches) {
          setSidebarState(false);
        }
      });

      mobileBreakpoint.addEventListener("change", function (e) {
        if (e.matches) {
          setSidebarState(false);
        } else {
          dashboardBody.classList.remove("sidebar-open");
          sidebarToggles.forEach(function (toggle) {
            toggle.setAttribute("aria-expanded", "false");
          });
        }
      });
    }

    // Add hover effects to table rows
    eventRows.forEach(function (row) {
      row.addEventListener("mouseenter", function () {
        this.style.cursor = "pointer";
      });

      row.addEventListener("mouseleave", function () {
        this.style.cursor = "default";
      });
    });

    // Enhanced action button interactions
    actionButtons.forEach(function (btn) {
      btn.addEventListener("mouseenter", function () {
        const icon = this.querySelector("i");
        if (icon) {
          icon.style.transform = "scale(1.1)";
        }
      });

      btn.addEventListener("mouseleave", function () {
        const icon = this.querySelector("i");
        if (icon) {
          icon.style.transform = "scale(1)";
        }
      });
    });

    // Sidebar navigation active state
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll(".sidebar-nav .nav-link");

		sidebarLinks.forEach(function(link) {
			const href = link.getAttribute('href');
			if (href && currentPath.includes(href)) {
				link.classList.add('active');
			} else {
				link.classList.remove('active');
			}
		});

    // Add keyboard shortcuts
    document.addEventListener("keydown", function (e) {
      // Ctrl/Cmd + N for new event
      if ((e.ctrlKey || e.metaKey) && e.key === "n") {
        e.preventDefault();
        const addEventBtn = document.querySelector('a[href="add_event.php"]');
        if (addEventBtn) {
          window.location.href = addEventBtn.href;
        }
      }
    });
  }
})();
