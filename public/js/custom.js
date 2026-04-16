// ==============================
// INIT PORTAL DROPDOWNS
// ==============================
function initDropdownPortal() {
    // Initialize each dropdown
    $(".ui.dropdown.action-dropdown").each(function () {
        const dropdown = $(this);

        dropdown.off("click.portal").on("click.portal", function (e) {
            e.stopPropagation();

            if (dropdown.data("portal-open")) {
                closeClonedPortal(dropdown);
            } else {
                openPortal(dropdown);
            }
        });
    });

    // Click outside dropdown → close all clones
    $(document)
        .off("click.portal-close")
        .on("click.portal-close", function (e) {
            const openedClones = $(".dropdown-portal"); // select cloned portals
            if (!openedClones.length) return;

            const target = $(e.target);
            if (
                target.closest(".ui.dropdown.action-dropdown").length ||
                target.closest(".dropdown-portal").length
            )
                return;

            openedClones.each(function () {
                $(this).remove(); // just remove the cloned portal
            });

            // Optionally, reset the original dropdowns
            $(".ui.dropdown.action-dropdown").each(function () {
                const dropdown = $(this);
                if (dropdown.data("portal-open")) {
                    dropdown.find(".menu").css("visibility", "visible");
                    dropdown.data("portal-open", false);
                    dropdown.removeData("portal-element");
                }
            });
        });
}

// Scroll / resize / zoom → close all cloned portals only
const closeAllClones = () => {
    $(".dropdown-portal").each(function () {
        const cloned = $(this);

        // Find the original dropdown linked to this clone
        const original = $(".ui.dropdown.action-dropdown").filter(function () {
            return (
                $(this).data("portal-element") &&
                $(this).data("portal-element")[0] === cloned[0]
            );
        });

        // Remove the cloned portal
        cloned.remove();

        // Restore the original dropdown menu visibility
        original.each(function () {
            $(this).find(".menu").css("visibility", "visible");
            $(this).data("portal-open", false);
            $(this).removeData("portal-element");
        });
    });
};

window.addEventListener("scroll", closeAllClones);

// Listen to all scrollable elements inside the document
document.querySelectorAll("*").forEach((el) => {
    const style = getComputedStyle(el);
    if (
        style.overflowY === "auto" ||
        style.overflowY === "scroll" ||
        style.overflowX === "auto" ||
        style.overflowX === "scroll"
    ) {
        el.addEventListener("scroll", closeAllClones);
    }
});

window.addEventListener("resize", () => {
    closeAllClones();
});

// ==============================
// OPEN PORTAL
// ==============================
function openPortal(dropdown) {
    const rect = dropdown[0].getBoundingClientRect();

    // Clone dropdown
    const cloned = dropdown
        .clone(true, true)
        .addClass("dropdown-portal")
        .removeClass("transition hidden");

    $("body").append(cloned);

    // Position clone
    cloned.css({
        position: "fixed",
        top: rect.top + "px",
        left: rect.left + "px",
        width: rect.width + "px",
        zIndex: 999999999,
    });

    cloned.find(".menu").addClass("visible active").show();

    // Hide original menu while clone is open
    dropdown.find(".menu").css("visibility", "hidden");

    dropdown.data("portal-open", true);
    dropdown.data("portal-element", cloned);
}

// ==============================
// CLOSE CLONED PORTAL ONLY
// ==============================
function closeClonedPortal(dropdown) {
    const cloned = dropdown.data("portal-element");
    if (cloned) cloned.remove();

    // Restore visibility of original menu
    dropdown.find(".menu").css("visibility", "visible");

    dropdown.data("portal-open", false);
    dropdown.removeData("portal-element");
}


async function initPreviewFile(dataList) {
    for (const dt of dataList) {
        if (!dt.filepath) continue;

        const container = document.getElementById('previewContainer_' + dt.id);
        if (!container) continue;

        const fileUrl = "/storage/" + dt.filepath;

        const typedArray = await fetch(fileUrl)
            .then(res => res.arrayBuffer())
            .then(buffer => new Uint8Array(buffer));

        const pdf = await pdfjsLib.getDocument(typedArray).promise;
        const page = await pdf.getPage(1);

        const fixedWidth = 120;
        const fixedHeight = 150;
        const viewport = page.getViewport({ scale: 1 });
        const scale = Math.min(fixedWidth / viewport.width, fixedHeight / viewport.height);
        const scaledViewport = page.getViewport({ scale });

        const canvas = document.createElement('canvas');
        canvas.width = fixedWidth;
        canvas.height = fixedHeight;

        const context = canvas.getContext('2d');
        context.fillStyle = '#fff';
        context.fillRect(0, 0, fixedWidth, fixedHeight);

        const offsetX = (fixedWidth - scaledViewport.width) / 2;
        const offsetY = (fixedHeight - scaledViewport.height) / 2;

        await page.render({
            canvasContext: context,
            viewport: scaledViewport,
            transform: [1, 0, 0, 1, offsetX, offsetY]
        }).promise;

        const wrapper = document.createElement('div');
        wrapper.className = 'pdf-wrapper';
        wrapper.style.width = fixedWidth + 'px';
        wrapper.style.height = fixedHeight + 'px';
        wrapper.appendChild(canvas);

        wrapper.addEventListener('click', () => window.open(fileUrl, '_blank'));
        container.appendChild(wrapper);
    }
}