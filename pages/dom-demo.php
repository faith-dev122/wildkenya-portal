<?php
// ============================================================
// WildKenya — DOM Manipulation Demo (pages/dom-demo.php)
// Week 3 Requirement: DOM manipulation, dynamic menu,
// hide/show elements, live text preview system
// ============================================================
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/nav.php';
?>

<!-- PAGE BANNER -->
<section class="py-4 text-white" style="background-color:#1a3c2e;">
    <div class="container">
        <h2 class="fw-bold mb-0">⚡ JavaScript DOM Manipulation Demo</h2>
        <p class="text-white-50 mb-0">
            Week 3 — BIT3208: Dynamic page interaction without page reload
        </p>
    </div>
</section>

<section class="py-5" style="background:#f4f6f9;">
    <div class="container">

        <!-- DEMO 1: Hide/Show Elements -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-eye-fill text-success me-2"></i>
                    Demo 1 — Hide / Show Elements
                </h4>
                <p class="text-muted small mb-4">
                    DOM manipulation to show or hide page sections dynamically
                    without reloading the page
                </p>

                <div class="d-flex gap-3 flex-wrap mb-4">
                    <button class="btn btn-success"
                            onclick="toggleElement('park-info')">
                        Toggle Park Info
                    </button>
                    <button class="btn btn-outline-success"
                            onclick="toggleElement('animal-info')">
                        Toggle Animal Info
                    </button>
                    <button class="btn btn-outline-secondary"
                            onclick="hideAll()">
                        Hide All
                    </button>
                    <button class="btn btn-secondary"
                            onclick="showAll()">
                        Show All
                    </button>
                </div>

                <div id="park-info" class="alert alert-success mb-3">
                    <strong>🗺️ Maasai Mara National Reserve</strong><br>
                    Kenya's most famous wildlife reserve — home to the Great Migration.
                    Entry fee: KES 800/adult (citizen).
                </div>

                <div id="animal-info" class="alert alert-warning mb-0">
                    <strong>🦁 African Lion — Panthera leo</strong><br>
                    Conservation Status: Vulnerable. Found in: Maasai Mara,
                    Amboseli, Tsavo East, Lake Nakuru, Nairobi NP.
                </div>
            </div>
        </div>

        <!-- DEMO 2: Live Text Preview -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-chat-text-fill text-success me-2"></i>
                    Demo 2 — Live Text Preview System
                </h4>
                <p class="text-muted small mb-4">
                    As you type a park review, the preview updates in real time
                    using DOM innerHTML — no form submission needed
                </p>

                <div class="row g-4">
                    <!-- Input Side -->
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-2">Write Your Review:</h6>
                        <input type="text" id="reviewer-name"
                               class="form-control mb-2"
                               placeholder="Your name..."
                               oninput="updatePreview()">
                        <select id="star-rating" class="form-select mb-2"
                                onchange="updatePreview()">
                            <option value="5">⭐⭐⭐⭐⭐ — 5 Stars</option>
                            <option value="4">⭐⭐⭐⭐ — 4 Stars</option>
                            <option value="3">⭐⭐⭐ — 3 Stars</option>
                            <option value="2">⭐⭐ — 2 Stars</option>
                            <option value="1">⭐ — 1 Star</option>
                        </select>
                        <textarea id="review-text"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Write your review here..."
                                  oninput="updatePreview()"></textarea>
                        <small class="text-muted" id="char-count">0 / 300 characters</small>
                    </div>

                    <!-- Live Preview Side -->
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-2">Live Preview:</h6>
                        <div id="review-preview"
                             class="p-3 border rounded-3 bg-white h-100"
                             style="min-height:160px;">
                            <p class="text-muted small fst-italic">
                                Start typing to see your review preview here...
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DEMO 3: Dynamic Dropdown Menu -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-list-ul text-success me-2"></i>
                    Demo 3 — Dynamic Menu (DOM innerHTML)
                </h4>
                <p class="text-muted small mb-4">
                    Clicking a region button dynamically builds a park list
                    in the DOM using JavaScript — no PHP, no page reload
                </p>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <button class="btn btn-success btn-sm"
                            onclick="filterParks('all')">
                        All Parks
                    </button>
                    <button class="btn btn-outline-success btn-sm"
                            onclick="filterParks('Rift Valley')">
                        Rift Valley
                    </button>
                    <button class="btn btn-outline-success btn-sm"
                            onclick="filterParks('Coast')">
                        Coast
                    </button>
                    <button class="btn btn-outline-success btn-sm"
                            onclick="filterParks('Central')">
                        Central
                    </button>
                    <button class="btn btn-outline-success btn-sm"
                            onclick="filterParks('Northern Kenya')">
                        Northern Kenya
                    </button>
                </div>

                <div id="parks-dynamic-list" class="row g-3">
                    <!-- Dynamically populated by JavaScript -->
                </div>
                <p class="text-muted small mt-2 mb-0" id="filter-count"></p>
            </div>
        </div>

        <!-- DEMO 4: Interactive Counter + Color Changer -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-1">
                    <i class="bi bi-arrows-expand text-success me-2"></i>
                    Demo 4 — Interactive DOM Manipulation
                </h4>
                <p class="text-muted small mb-4">
                    Real-time DOM changes — counter, colour switcher,
                    and dynamic text content updates
                </p>

                <div class="row g-4">
                    <!-- Counter -->
                    <div class="col-md-4 text-center">
                        <h6 class="fw-bold">Safari Day Counter</h6>
                        <div id="counter-display"
                             class="display-4 fw-bold my-3"
                             style="color:#2d6a4f;">0</div>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-success"
                                    onclick="changeCounter(1)">
                                <i class="bi bi-plus-lg"></i> Day
                            </button>
                            <button class="btn btn-outline-danger"
                                    onclick="changeCounter(-1)">
                                <i class="bi bi-dash-lg"></i> Day
                            </button>
                            <button class="btn btn-outline-secondary"
                                    onclick="resetCounter()">
                                Reset
                            </button>
                        </div>
                        <p class="small text-muted mt-2" id="counter-label">
                            No safari days planned
                        </p>
                    </div>

                    <!-- Colour Switcher -->
                    <div class="col-md-4 text-center">
                        <h6 class="fw-bold">Theme Colour Switcher</h6>
                        <div id="colour-box"
                             class="rounded-3 my-3 mx-auto d-flex align-items-center
                                    justify-content-center fw-bold text-white"
                             style="width:120px;height:120px;background:#2d6a4f;
                                    font-size:12px;">
                            WildKenya Theme
                        </div>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <button class="btn btn-sm" style="background:#2d6a4f;color:white;"
                                    onclick="changeColour('#2d6a4f','Forest Green')">
                                Forest
                            </button>
                            <button class="btn btn-sm btn-warning"
                                    onclick="changeColour('#d4a017','Savannah Gold')">
                                Savannah
                            </button>
                            <button class="btn btn-sm btn-danger"
                                    onclick="changeColour('#c0392b','Sunset Red')">
                                Sunset
                            </button>
                            <button class="btn btn-sm btn-dark"
                                    onclick="changeColour('#1a1a2e','Night Sky')">
                                Night
                            </button>
                        </div>
                    </div>

                    <!-- Live Clock -->
                    <div class="col-md-4 text-center">
                        <h6 class="fw-bold">Live Clock (setInterval DOM Update)</h6>
                        <div id="live-clock"
                             class="display-6 fw-bold my-3"
                             style="color:#2d6a4f; font-family:monospace;">
                            00:00:00
                        </div>
                        <p class="small text-muted">
                            Updated every second using<br>
                            <code>setInterval()</code> + <code>innerHTML</code>
                        </p>
                        <div id="live-date" class="small text-muted"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Code Explanation -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-code-slash text-success me-2"></i>
                    Key DOM Methods Used in This Page
                </h5>
                <div class="row g-3 small">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <code class="d-block mb-1">document.getElementById('id')</code>
                            <span class="text-muted">Selects a single HTML element by its id</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <code class="d-block mb-1">element.style.display = 'none'</code>
                            <span class="text-muted">Hides an element from the page (Demo 1)</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <code class="d-block mb-1">element.innerHTML = '...'</code>
                            <span class="text-muted">Replaces the content inside an element (Demo 2, 3)</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <code class="d-block mb-1">element.style.backgroundColor = '#...'</code>
                            <span class="text-muted">Changes an element's colour dynamically (Demo 4)</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <code class="d-block mb-1">element.textContent = '...'</code>
                            <span class="text-muted">Updates text content without HTML parsing</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <code class="d-block mb-1">setInterval(function, 1000)</code>
                            <span class="text-muted">Runs a function every 1000ms — used for live clock</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ============================================================
     JAVASCRIPT — All DOM Manipulation Code
============================================================ -->
<script>
// ============================================================
// DEMO 1 — Hide / Show Elements
// ============================================================
function toggleElement(id) {
    const el = document.getElementById(id);
    if (el.style.display === 'none') {
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}

function hideAll() {
    document.getElementById('park-info').style.display   = 'none';
    document.getElementById('animal-info').style.display = 'none';
}

function showAll() {
    document.getElementById('park-info').style.display   = 'block';
    document.getElementById('animal-info').style.display = 'block';
}

// ============================================================
// DEMO 2 — Live Text Preview
// ============================================================
function updatePreview() {
    const name    = document.getElementById('reviewer-name').value || 'Anonymous';
    const rating  = document.getElementById('star-rating').value;
    const text    = document.getElementById('review-text').value;
    const preview = document.getElementById('review-preview');
    const counter = document.getElementById('char-count');

    // Update character count
    counter.textContent = text.length + ' / 300 characters';
    if (text.length > 250) counter.style.color = 'red';
    else counter.style.color = '';

    // Build star string
    let stars = '';
    for (let i = 0; i < parseInt(rating); i++) stars += '⭐';

    // Build preview HTML using innerHTML
    if (text.trim() === '') {
        preview.innerHTML = '<p class="text-muted small fst-italic">Start typing to see your review preview here...</p>';
    } else {
        preview.innerHTML = `
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="rounded-circle bg-success text-white d-flex
                             align-items-center justify-content-center fw-bold"
                     style="width:36px;height:36px;font-size:13px;">
                    ${name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <strong>${name}</strong>
                    <div>${stars}</div>
                </div>
            </div>
            <p class="mb-0 small">${text}</p>
        `;
    }
}

// ============================================================
// DEMO 3 — Dynamic Park Menu
// ============================================================
const allParks = [
    { name: 'Maasai Mara National Reserve',  region: 'Rift Valley',    fee: 800  },
    { name: 'Amboseli National Park',         region: 'Rift Valley',    fee: 800  },
    { name: 'Lake Nakuru National Park',      region: 'Rift Valley',    fee: 800  },
    { name: 'Hell\'s Gate National Park',     region: 'Rift Valley',    fee: 215  },
    { name: 'Tsavo East National Park',       region: 'Coast',          fee: 800  },
    { name: 'Tsavo West National Park',       region: 'Coast',          fee: 800  },
    { name: 'Aberdare National Park',         region: 'Central',        fee: 600  },
    { name: 'Mount Kenya National Park',      region: 'Central',        fee: 600  },
    { name: 'Nairobi National Park',          region: 'Central',        fee: 430  },
    { name: 'Samburu National Reserve',       region: 'Northern Kenya', fee: 800  },
];

function filterParks(region) {
    const list    = document.getElementById('parks-dynamic-list');
    const counter = document.getElementById('filter-count');

    const filtered = region === 'all'
        ? allParks
        : allParks.filter(p => p.region === region);

    let html = '';
    filtered.forEach(park => {
        html += `
            <div class="col-md-6 col-lg-4">
                <div class="p-3 border rounded-3 bg-white d-flex
                             align-items-center gap-3">
                    <div style="font-size:1.5rem;">🌍</div>
                    <div>
                        <p class="fw-bold small mb-0">${park.name}</p>
                        <small class="text-muted">
                            ${park.region} — KES ${park.fee.toLocaleString()}/adult
                        </small>
                    </div>
                </div>
            </div>
        `;
    });

    // Update DOM using innerHTML
    list.innerHTML    = html;
    counter.textContent = `Showing ${filtered.length} park${filtered.length !== 1 ? 's' : ''}`;
}

// Load all parks on page load
filterParks('all');

// ============================================================
// DEMO 4 — Counter + Colour + Clock
// ============================================================
let safariDays = 0;

function changeCounter(delta) {
    safariDays += delta;
    if (safariDays < 0) safariDays = 0;

    document.getElementById('counter-display').textContent = safariDays;

    const label = document.getElementById('counter-label');
    if      (safariDays === 0)         label.textContent = 'No safari days planned';
    else if (safariDays === 1)         label.textContent = '1 safari day planned 🦁';
    else if (safariDays <= 3)          label.textContent = safariDays + ' days — short safari 🦒';
    else if (safariDays <= 7)          label.textContent = safariDays + ' days — full safari 🐘';
    else                               label.textContent = safariDays + ' days — epic expedition 🌍';
}

function resetCounter() {
    safariDays = 0;
    document.getElementById('counter-display').textContent = 0;
    document.getElementById('counter-label').textContent   = 'No safari days planned';
}

function changeColour(hex, name) {
    const box      = document.getElementById('colour-box');
    box.style.backgroundColor = hex;
    box.textContent            = name;
}

// Live clock using setInterval
function updateClock() {
    const now  = new Date();
    const h    = String(now.getHours()).padStart(2, '0');
    const m    = String(now.getMinutes()).padStart(2, '0');
    const s    = String(now.getSeconds()).padStart(2, '0');
    const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    document.getElementById('live-clock').textContent =
        `${h}:${m}:${s}`;
    document.getElementById('live-date').textContent  =
        `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
}

// Update every second
setInterval(updateClock, 1000);
updateClock(); // Run immediately on load
</script>

<?php require_once '../includes/footer.php'; ?>
