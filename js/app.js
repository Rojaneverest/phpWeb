document.addEventListener("DOMContentLoaded", () => {
  // Slider Functionality (Home Section)
  const slider = document.querySelector(".slider");
  if (slider) {
    const images = document.querySelectorAll(".slider-image");
    let current = 0;

    function showNextImage() {
      images[current].classList.remove("active");
      current = (current + 1) % images.length;
      images[current].classList.add("active");
    }

    setInterval(showNextImage, 3000);
  }

  // Task Manager Functionality
  const taskForm = document.getElementById("task-form");
  const taskInput = document.getElementById("task-input");
  const taskList = document.getElementById("task-list");

  if (taskForm && taskList) {
    // Add Task
    taskForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const taskName = taskInput.value.trim();
      if (!taskName) return;

      fetch("index.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=add&task_name=${encodeURIComponent(taskName)}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            const li = document.createElement("li");
            li.className = "task-item";
            li.dataset.id = data.id;
            li.innerHTML = `
                        <span>${taskName}</span>
                        <button class="complete-btn">✓</button>
                        <button class="delete-btn">✗</button>
                    `;
            taskList.appendChild(li);
            taskInput.value = "";
          }
        });
    });

    // Complete or Delete Task
    taskList.addEventListener("click", (e) => {
      const li = e.target.closest(".task-item");
      if (!li) return;
      const id = li.dataset.id;

      if (e.target.classList.contains("complete-btn")) {
        fetch("index.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `action=complete&id=${id}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              li.classList.toggle("completed");
            }
          });
      } else if (e.target.classList.contains("delete-btn")) {
        fetch("index.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `action=delete&id=${id}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              li.remove();
            }
          });
      }
    });

    // Drag-and-Drop
    if (typeof Sortable !== "undefined") {
      Sortable.create(taskList, {
        animation: 150,
        onEnd: (evt) => {
          const items = Array.from(taskList.children);
          const order = items.map((item, index) => ({
            id: item.dataset.id,
            order: index,
          }));

          fetch("index.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "reorder", order }),
          });
        },
      });
    }
  }

  // Contact Form Validation
  const contactForm = document.getElementById("contact-form");
  if (contactForm) {
    contactForm.addEventListener("submit", (e) => {
      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim();
      const message = document.getElementById("message").value.trim();

      // Only name and message are required
      if (!name || !message) {
        e.preventDefault();
        alert("Please fill in name and message fields.");
        return;
      }

      // If email is provided, validate it
      if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        e.preventDefault();
        alert("Please enter a valid email address or leave it empty.");
      }
    });
  }
});
