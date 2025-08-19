// Global variables
let cart = JSON.parse(localStorage.getItem("cart")) || []

// DOM Content Loaded
document.addEventListener("DOMContentLoaded", () => {
  updateCartCount()
  initializeEventListeners()
})

// Initialize event listeners
function initializeEventListeners() {
  // Search functionality
  const searchBtn = document.querySelector(".search-btn")
  const searchInput = document.querySelector(".search-bar input")

  if (searchBtn) {
    searchBtn.addEventListener("click", performSearch)
  }

  if (searchInput) {
    searchInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        performSearch()
      }
    })
  }

  // Add to cart buttons
  const addToCartBtns = document.querySelectorAll(".add-to-cart")
  addToCartBtns.forEach((btn) => {
    btn.addEventListener("click", addToCart)
  })

  // Quantity controls
  const quantityBtns = document.querySelectorAll(".quantity-btn")
  quantityBtns.forEach((btn) => {
    btn.addEventListener("click", updateQuantity)
  })

  // Remove item buttons
  const removeItemBtns = document.querySelectorAll(".remove-item")
  removeItemBtns.forEach((btn) => {
    btn.addEventListener("click", removeFromCart)
  })

  // Form validations
  const forms = document.querySelectorAll("form")
  forms.forEach((form) => {
    form.addEventListener("submit", validateForm)
  })
}

// Search functionality
function performSearch() {
  const searchInput = document.querySelector(".search-bar input")
  const query = searchInput.value.trim()

  if (query) {
    window.location.href = `search.php?q=${encodeURIComponent(query)}`
  }
}

// Add to cart functionality
function addToCart(e) {
    // If the clicked element is a link, let the default action proceed
    if (e.target.tagName === 'A') {
        return;
    }
  e.preventDefault()

  const productCard = e.target.closest(".product-card")
  const productId = productCard.dataset.productId
  const productName = productCard.querySelector(".product-name").textContent
  const productPrice = Number.parseFloat(productCard.querySelector(".current-price").textContent.replace("$", ""))
  const productImage = productCard.querySelector(".product-image").style.backgroundImage || ""

  // Check if item already exists in cart
  const existingItem = cart.find((item) => item.id === productId)

  if (existingItem) {
    existingItem.quantity += 1
  } else {
    cart.push({
      id: productId,
      name: productName,
      price: productPrice,
      image: productImage,
      quantity: 1,
    })
  }

  // Save to localStorage
  localStorage.setItem("cart", JSON.stringify(cart))

  // Update cart count
  updateCartCount()

  // Show success message
  showAlert("Product added to cart!", "success")

  // Add animation to button
  e.target.innerHTML = '<span class="loading"></span> Added!'
  setTimeout(() => {
    e.target.innerHTML = "Add to Cart"
  }, 1500)
}

// Update cart count
function updateCartCount() {
  const cartCountElement = document.querySelector(".cart-count")
  if (cartCountElement) {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0)
    cartCountElement.textContent = totalItems
    cartCountElement.style.display = totalItems > 0 ? "inline" : "none"
  }
}

// Update quantity
function updateQuantity(e) {
  const cartItem = e.target.closest(".cart-item")
  const productId = cartItem.dataset.productId
  const action = e.target.dataset.action
  const quantityElement = cartItem.querySelector(".quantity")

  const cartItemIndex = cart.findIndex((item) => item.id === productId)

  if (cartItemIndex !== -1) {
    if (action === "increase") {
      cart[cartItemIndex].quantity += 1
    } else if (action === "decrease" && cart[cartItemIndex].quantity > 1) {
      cart[cartItemIndex].quantity -= 1
    }

    quantityElement.textContent = cart[cartItemIndex].quantity
    localStorage.setItem("cart", JSON.stringify(cart))
    updateCartCount()
    updateCartTotal()
  }
}

// Remove from cart
function removeFromCart(e) {
  const cartItem = e.target.closest(".cart-item")
  const productId = cartItem.dataset.productId

  cart = cart.filter((item) => item.id !== productId)
  localStorage.setItem("cart", JSON.stringify(cart))

  cartItem.remove()
  updateCartCount()
  updateCartTotal()

  showAlert("Item removed from cart", "info")
}

// Update cart total
function updateCartTotal() {
  const totalElement = document.querySelector(".cart-total")
  if (totalElement) {
    const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0)
    totalElement.textContent = `$${total.toFixed(2)}`
  }
}

// Form validation
function validateForm(e) {
  const form = e.target
  const requiredFields = form.querySelectorAll("[required]")
  let isValid = true

  requiredFields.forEach((field) => {
    if (!field.value.trim()) {
      isValid = false
      field.style.borderColor = "#ff4757"
      showFieldError(field, "This field is required")
    } else {
      field.style.borderColor = "#e1e5e9"
      hideFieldError(field)
    }
  })

  // Email validation
  const emailFields = form.querySelectorAll('input[type="email"]')
  emailFields.forEach((field) => {
    if (field.value && !isValidEmail(field.value)) {
      isValid = false
      field.style.borderColor = "#ff4757"
      showFieldError(field, "Please enter a valid email address")
    }
  })

  // Password confirmation
  const password = form.querySelector('input[name="password"]')
  const confirmPassword = form.querySelector('input[name="confirm_password"]')

  if (password && confirmPassword && password.value !== confirmPassword.value) {
    isValid = false
    confirmPassword.style.borderColor = "#ff4757"
    showFieldError(confirmPassword, "Passwords do not match")
  }

  if (!isValid) {
    e.preventDefault()
  }
}

// Email validation helper
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

// Show field error
function showFieldError(field, message) {
  let errorElement = field.parentNode.querySelector(".field-error")
  if (!errorElement) {
    errorElement = document.createElement("div")
    errorElement.className = "field-error"
    errorElement.style.color = "#ff4757"
    errorElement.style.fontSize = "0.9rem"
    errorElement.style.marginTop = "5px"
    field.parentNode.appendChild(errorElement)
  }
  errorElement.textContent = message
}

// Hide field error
function hideFieldError(field) {
  const errorElement = field.parentNode.querySelector(".field-error")
  if (errorElement) {
    errorElement.remove()
  }
}

// Show alert messages
function showAlert(message, type = "info") {
  const alertDiv = document.createElement("div")
  alertDiv.className = `alert alert-${type}`
  alertDiv.textContent = message

  // Insert at the top of the main content
  const main = document.querySelector("main") || document.body
  main.insertBefore(alertDiv, main.firstChild)

  // Auto remove after 5 seconds
  setTimeout(() => {
    alertDiv.remove()
  }, 5000)

  // Scroll to top to show the alert
  window.scrollTo({ top: 0, behavior: "smooth" })
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault()
    const target = document.querySelector(this.getAttribute("href"))
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      })
    }
  })
})

// Lazy loading for images
function lazyLoadImages() {
  const images = document.querySelectorAll("img[data-src]")
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target
        img.src = img.dataset.src
        img.removeAttribute("data-src")
        imageObserver.unobserve(img)
      }
    })
  })

  images.forEach((img) => imageObserver.observe(img))
}

// Initialize lazy loading
if ("IntersectionObserver" in window) {
  lazyLoadImages()
}

// Mobile menu toggle
function toggleMobileMenu() {
  const mobileMenu = document.querySelector(".mobile-menu")
  if (mobileMenu) {
    mobileMenu.classList.toggle("active")
  }
}

// Price formatting
function formatPrice(price) {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(price)
}

// Date formatting
function formatDate(dateString) {
  const options = {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  }
  return new Date(dateString).toLocaleDateString("en-US", options)
}

// Copy to clipboard functionality
function copyToClipboard(text) {
  navigator.clipboard
    .writeText(text)
    .then(() => {
      showAlert("Copied to clipboard!", "success")
    })
    .catch(() => {
      showAlert("Failed to copy to clipboard", "error")
    })
}

// Initialize tooltips
function initializeTooltips() {
  const tooltipElements = document.querySelectorAll("[data-tooltip]")
  tooltipElements.forEach((element) => {
    element.addEventListener("mouseenter", showTooltip)
    element.addEventListener("mouseleave", hideTooltip)
  })
}

function showTooltip(e) {
  const tooltip = document.createElement("div")
  tooltip.className = "tooltip"
  tooltip.textContent = e.target.dataset.tooltip
  tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 0.9rem;
        z-index: 1000;
        pointer-events: none;
    `

  document.body.appendChild(tooltip)

  const rect = e.target.getBoundingClientRect()
  tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + "px"
  tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + "px"

  e.target.tooltip = tooltip
}

function hideTooltip(e) {
  if (e.target.tooltip) {
    e.target.tooltip.remove()
    delete e.target.tooltip
  }
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
  initializeTooltips()
})