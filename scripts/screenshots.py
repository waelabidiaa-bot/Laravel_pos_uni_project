from selenium import webdriver
from selenium.webdriver.firefox.options import Options
from selenium.webdriver.firefox.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException, WebDriverException

from pathlib import Path
import os
import time


# ============================================================
# CONFIGURATION
# ============================================================

BASE_URL = "http://pos_laravel_project.test"

# Administrator credentials
ADMIN_EMAIL = os.getenv("POS_ADMIN_EMAIL", "admin@example.com")
ADMIN_PASSWORD = os.getenv("POS_ADMIN_PASSWORD", "password123")

# Cashier credentials
CASHIER_EMAIL = os.getenv("POS_CASHIER_EMAIL", "wael@example.com")
CASHIER_PASSWORD = os.getenv("POS_CASHIER_PASSWORD", "testtest")

GECKODRIVER = "/snap/bin/geckodriver"

HEADLESS = False
WINDOW_WIDTH = 1440
WINDOW_HEIGHT = 1000
WAIT_TIMEOUT = 20

SCREENSHOT_ROOT = Path("screenshots")

ADMIN_SCREENSHOT_DIR = SCREENSHOT_ROOT / "admin"
CASHIER_SCREENSHOT_DIR = SCREENSHOT_ROOT / "cashier"

ADMIN_SCREENSHOT_DIR.mkdir(parents=True, exist_ok=True)
CASHIER_SCREENSHOT_DIR.mkdir(parents=True, exist_ok=True)


# ============================================================
# PAGE CONFIGURATION
# ============================================================

# Administrator pages
ADMIN_PAGES = {
    "dashboard": "/admin",
    "products": "/admin/products",
    "categories": "/admin/categories",
    "statistics": "/admin/statistics",
    "settings": "/parametre",
    "ai-insights": "/admin/ai-insights",
    "cashiers": "/admin/cashiers",
    "products-details": "/admin/products",  # Example product ID for testing

}

# Cashier pages
#
# Update these paths if your Laravel route:list shows different
# URLs. The POS page path should be the actual checkout route.
CASHIER_PAGES = {
    "pos": "/cashier",
    
    "sales-details": "/cashier/sales/21",  # Example sale ID for testing

}


# ============================================================
# FIREFOX CONFIGURATION
# ============================================================

options = Options()

if HEADLESS:
    options.add_argument("-headless")

service = Service(GECKODRIVER)

driver = webdriver.Firefox(
    service=service,
    options=options,
)

driver.set_window_size(WINDOW_WIDTH, WINDOW_HEIGHT)

wait = WebDriverWait(driver, WAIT_TIMEOUT)


# ============================================================
# HELPERS
# ============================================================

def wait_for_page():
    """Wait until the browser finishes loading the document."""

    wait.until(
        lambda browser: browser.execute_script(
            "return document.readyState"
        ) == "complete"
    )


def safe_filename(name):
    """Make a safe filename for screenshots."""

    return "".join(
        character if character.isalnum() or character in "-_"
        else "_"
        for character in name
    )


def take_screenshot(name, directory):
    """Capture a full-page Firefox screenshot."""

    time.sleep(1)

    path = directory / f"{safe_filename(name)}.png"

    driver.save_screenshot(str(path))

    print(f"  ✓ Screenshot saved: {path}")


def open_page(name, path, directory):
    """Open a page, verify the result, and capture it."""

    print(f"\nOpening {name}: {path}")

    driver.get(BASE_URL + path)
    wait_for_page()

    current_url = driver.current_url

    if "/login" in current_url:
        raise RuntimeError(
            f"Redirected to login while opening {path}"
        )

    if driver.find_elements(By.CSS_SELECTOR, ".exception"):
        raise RuntimeError(
            f"Laravel exception page detected at {current_url}"
        )

    take_screenshot(name, directory)


def login(email, password):
    """Log in through the Laravel login form."""

    print(f"\nLogging in as {email}...")

    driver.get(BASE_URL + "/login")
    wait_for_page()

    email_input = wait.until(
        EC.visibility_of_element_located(
            (By.ID, "email")
        )
    )

    password_input = wait.until(
        EC.visibility_of_element_located(
            (By.ID, "password")
        )
    )

    email_input.clear()
    email_input.send_keys(email)

    password_input.clear()
    password_input.send_keys(password)

    form = wait.until(
        EC.presence_of_element_located(
            (By.CSS_SELECTOR, "form")
        )
    )

    form.submit()

    # Wait for Laravel to redirect away from the login page.
    wait.until(
        lambda browser: "/login" not in browser.current_url
    )

    wait_for_page()

    print(f"  ✓ Login redirect: {driver.current_url}")


def logout():
    """
    Log out using Laravel's logout route.

    First try a visible logout form or button.
    If the application uses a different logout UI, adjust this
    helper to match the actual form in your Blade layout.
    """

    print("\nLogging out...")

    # Common Laravel pattern: POST form to /logout.
    logout_forms = driver.find_elements(
        By.CSS_SELECTOR,
        'form[action$="/logout"]'
    )

    if logout_forms:
        logout_forms[0].submit()
    else:
        logout_buttons = driver.find_elements(
            By.XPATH,
            (
                "//button[contains(translate(normalize-space(.),"
                "'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz'),"
                "'logout')]"
                " | "
                "//a[contains(translate(normalize-space(.),"
                "'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz'),"
                "'logout')]"
            )
        )

        if logout_buttons:
            logout_buttons[0].click()
        else:
            # Fallback: navigate to the login page.
            # This does not itself invalidate an authenticated
            # Laravel session, so it is not a true logout.
            raise RuntimeError(
                "Could not find a logout form or button. "
                "Update logout() to match your application."
            )

    try:
        wait.until(
            lambda browser: "/login" in browser.current_url
        )
    except TimeoutException:
        # Verify whether logout actually succeeded.
        driver.get(BASE_URL + "/login")
        wait_for_page()

        if "/login" not in driver.current_url:
            raise RuntimeError(
                "Logout could not be verified."
            )

    print("  ✓ Logged out")


def capture_pages(pages, directory, role_name):
    """Capture all configured pages for one role."""

    print("\n" + "=" * 55)
    print(f"Capturing {role_name.upper()} pages")
    print("=" * 55)

    failures = []

    for name, path in pages.items():
        try:
            open_page(name, path, directory)

        except Exception as error:
            print(f"  ✗ Failed: {name} — {error}")

            failures.append({
                "name": name,
                "path": path,
                "error": str(error),
            })

    return failures


# ============================================================
# MAIN
# ============================================================

all_failures = []

try:

    print("=" * 55)
    print("LARAVEL POS SCREENSHOT AUTOMATION")
    print("=" * 55)

    # --------------------------------------------------------
    # ADMIN SESSION
    # --------------------------------------------------------

    login(ADMIN_EMAIL, ADMIN_PASSWORD)

    admin_failures = capture_pages(
        ADMIN_PAGES,
        ADMIN_SCREENSHOT_DIR,
        "admin",
    )

    all_failures.extend(
        ("admin", failure)
        for failure in admin_failures
    )

    # --------------------------------------------------------
    # LOG OUT ADMIN
    # --------------------------------------------------------

    logout()

    # --------------------------------------------------------
    # CASHIER SESSION
    # --------------------------------------------------------

    login(CASHIER_EMAIL, CASHIER_PASSWORD)

    cashier_failures = capture_pages(
        CASHIER_PAGES,
        CASHIER_SCREENSHOT_DIR,
        "cashier",
    )

    all_failures.extend(
        ("cashier", failure)
        for failure in cashier_failures
    )

    # --------------------------------------------------------
    # SUMMARY
    # --------------------------------------------------------

    print("\n" + "=" * 55)
    print("SCREENSHOT AUTOMATION FINISHED")
    print("=" * 55)

    print(f"\nAdmin screenshots: {ADMIN_SCREENSHOT_DIR.absolute()}")
    print(f"Cashier screenshots: {CASHIER_SCREENSHOT_DIR.absolute()}")

    if all_failures:
        print(f"\nFailed pages: {len(all_failures)}")

        for role, failure in all_failures:
            print(
                f"  - [{role}] {failure['name']}: "
                f"{failure['error']}"
            )
    else:
        print("\nAll configured pages were captured successfully.")

finally:

    driver.quit()
    print("\nFirefox closed.")