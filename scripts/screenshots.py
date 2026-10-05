from selenium import webdriver
from selenium.webdriver.firefox.options import Options
from selenium.webdriver.firefox.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

from pathlib import Path
import time


# ============================================================
# CONFIGURATION
# ============================================================

BASE_URL = "http://pos_laravel_project.test"

EMAIL = "admin@example.com"
PASSWORD = "password123"

GECKODRIVER = "/snap/bin/geckodriver"

SCREENSHOT_DIR = Path("screenshots")
SCREENSHOT_DIR.mkdir(exist_ok=True)

# Pages to capture
PAGES = {
    "dashboard": "/admin",
    "products": "/admin/products",
    "categories": "/admin/categories",
    "statistics": "/admin/statistics",
    "settings": "/parametre",
}


# ============================================================
# FIREFOX
# ============================================================

options = Options()

# True = Firefox is visible
# False = run in background
HEADLESS = False

if HEADLESS:
    options.add_argument("-headless")

service = Service(GECKODRIVER)

driver = webdriver.Firefox(
    service=service,
    options=options
)

# Set browser window size
driver.set_window_size(1440, 900)

wait = WebDriverWait(driver, 15)


# ============================================================
# HELPERS
# ============================================================

def wait_for_page():
    """Wait until the browser finishes loading the page."""

    wait.until(
        lambda d: d.execute_script(
            "return document.readyState"
        ) == "complete"
    )


def take_screenshot(name):
    """Take a full-page screenshot."""

    # Give the UI a moment to finish rendering
    time.sleep(1)

    path = SCREENSHOT_DIR / f"{name}.png"

    driver.save_full_page_screenshot(str(path))

    print(f"✓ Saved: {path}")


def open_page(name, path):
    """Open a page and take a screenshot."""

    print(f"\nOpening {name}...")

    driver.get(BASE_URL + path)

    wait_for_page()

    # Make sure authentication is still active
    if "/login" in driver.current_url:
        raise RuntimeError(
            f"Authentication lost while opening {path}"
        )

    take_screenshot(name)


# ============================================================
# MAIN
# ============================================================

try:

    # --------------------------------------------------------
    # LOGIN
    # --------------------------------------------------------

    print("Starting Firefox...")

    driver.get(BASE_URL + "/login")

    wait_for_page()

    print("Logging in...")

    # Find email
    email_input = wait.until(
        EC.presence_of_element_located(
            (By.ID, "email")
        )
    )

    # Find password
    password_input = wait.until(
        EC.presence_of_element_located(
            (By.ID, "password")
        )
    )

    # Enter credentials
    email_input.clear()
    email_input.send_keys(EMAIL)

    password_input.clear()
    password_input.send_keys(PASSWORD)

    # Your form has:
    #
    # <form method="POST" action="{{ route('login.store') }}">
    #
    # So submit the form directly.
    form = wait.until(
        EC.presence_of_element_located(
            (By.CSS_SELECTOR, "form")
        )
    )

    form.submit()

    # Wait until Laravel redirects away from /login
    wait.until(
        lambda d: "/login" not in d.current_url
    )

    print("✓ Login successful")

    # --------------------------------------------------------
    # SCREENSHOTS
    # --------------------------------------------------------

    for name, path in PAGES.items():

        try:

            open_page(name, path)

        except Exception as error:

            print(
                f"✗ Failed to capture {name}: {error}"
            )

    # --------------------------------------------------------
    # DONE
    # --------------------------------------------------------

    print("\n===================================")
    print("Screenshot automation completed!")
    print("===================================")

    print(
        f"\nScreenshots saved to:\n"
        f"{SCREENSHOT_DIR.absolute()}"
    )


finally:

    driver.quit()

    print("\nFirefox closed.")