// Handle menu interactions
document.addEventListener('DOMContentLoaded', () => {
    // Profile menu
    const profileMenuButton = document.getElementById('profile-menu-button');
    const profileMenu = document.getElementById('profile-menu');
    
    if (profileMenuButton && profileMenu) {
        profileMenuButton.addEventListener('click', () => {
            profileMenu.classList.toggle('hidden');
        });

        // Close profile menu when clicking outside
        document.addEventListener('click', (event) => {
            if (!profileMenuButton.contains(event.target) && !profileMenu.contains(event.target)) {
                profileMenu.classList.add('hidden');
            }
        });
    }

    // Wallet menu
    const walletMenuButton = document.getElementById('wallet-menu-button');
    const walletMenu = document.getElementById('wallet-menu');
    
    if (walletMenuButton && walletMenu) {
        walletMenuButton.addEventListener('click', () => {
            walletMenu.classList.toggle('hidden');
        });

        // Close wallet menu when clicking outside
        document.addEventListener('click', (event) => {
            if (!walletMenuButton.contains(event.target) && !walletMenu.contains(event.target)) {
                walletMenu.classList.add('hidden');
            }
        });
    }

    // Mobile menu
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const header = document.getElementById('main-header');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Handle header visibility on scroll
    let lastScroll = 0;
    if (header) {
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            if (currentScroll <= 0) {
                header.classList.remove('-translate-y-full');
            } else if (currentScroll > lastScroll) {
                header.classList.add('-translate-y-full');
            } else {
                header.classList.remove('-translate-y-full');
            }
            lastScroll = currentScroll;
        });
    }
});

// Function to handle currency selection
function selectCurrency(currency, balance) {
    // Update the wallet button display
    const walletButton = document.getElementById('wallet-menu-button');
    if (walletButton) {
        const displaySpan = walletButton.querySelector('span');
        if (displaySpan) {
            displaySpan.textContent = balance;
        }
    }

    // Close the wallet menu
    const walletMenu = document.getElementById('wallet-menu');
    if (walletMenu) {
        walletMenu.classList.add('hidden');
    }

    // Store the selected currency preference
    fetch('/wallet/set-display-currency', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ currency })
    });
}

// Make selectCurrency available globally
window.selectCurrency = selectCurrency;
