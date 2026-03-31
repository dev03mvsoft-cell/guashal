/**
 * Donation Chronicle - Real-time Sacred Social Proof
 * Displays a premium toast notification on the bottom-left of the sanctuary.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Fetch donations from the backend
    fetch('/api/get_recent_donations.php')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(donations => {
            // Check for mobile - don't show on small screens
            if (window.innerWidth < 768) {
                console.log("Chronicle disabled for mobile screens.");
                return;
            }

            if (donations && donations.length > 0) {
                initDonationTicker(donations);
            }
        });
});

function initDonationTicker(donations) {
    let index = 0;
    let isActive = false;
    
    // Create the container if it doesn't exist
    let container = document.getElementById('donation-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'donation-toast-container';
        container.style.cssText = 'position: fixed; bottom: 30px; left: 30px; z-index: 9999; pointer-events: none; transition: opacity 0.5s ease; opacity: 0;';
        document.body.appendChild(container);
    }

    // Scroll Logic: Only show after passing Header/Hero
    window.addEventListener('scroll', () => {
        // Assume Hero is roughly 600px or find first section height
        const scrollThreshold = window.innerHeight * 0.8; 
        if (window.scrollY > scrollThreshold) {
            container.style.opacity = '1';
            container.style.pointerEvents = 'auto';
            if (!isActive) {
                isActive = true;
                setTimeout(showNextToast, 1000); 
                setInterval(showNextToast, 15000);
            }
        } else {
            container.style.opacity = '0';
            container.style.pointerEvents = 'none';
        }
    });

    // Function to show the next toast
    function showNextToast() {
        if (container.style.opacity === '0') return; // Don't trigger if hidden
        const d = donations[index];
        index = (index + 1) % donations.length;

        const toast = document.createElement('div');
        toast.className = 'donation-toast';
        toast.style.cssText = `
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            padding: 0.75rem 1.25rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 1rem;
            border-left: 4px solid #FF6A00;
            margin-bottom: 0.75rem;
            transform: translateX(-120%);
            transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
            max-width: 280px;
            pointer-events: auto;
        `;

        toast.innerHTML = `
            <div style="background: #fff5eb; width: 34px; height: 34px; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-heart text-[#FF6A00]" style="font-size: 0.8rem;"></i>
            </div>
            <div style="flex: 1;">
                <p style="margin: 0; font-weight: 700; font-size: 0.75rem; color: #2c4c3b; text-transform: uppercase; letter-spacing: 0.2px; line-height: 1;">${d.donor_name}</p>
                <p style="margin: 2px 0 0; font-size: 0.7rem; color: #666; line-height: 1.2;">Blessed with <span style="color: #FF6A00; font-weight: 800;">₹${parseInt(d.amount).toLocaleString()}</span></p>
            </div>
        `;

        container.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        }, 100);

        // After 6 seconds
        setTimeout(() => {
            toast.style.transform = 'translateX(-120%)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 800);
        }, 6000);
    }
}
