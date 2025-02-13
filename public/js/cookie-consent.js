// Vérifie si le consentement aux cookies a déjà été donné
if (!document.cookie.split('; ').find(row => row.startsWith('cookie_consent='))) {
    // Créer le popup HTML pour demander le consentement
    var cookiePopup = document.createElement('div');
    cookiePopup.id = 'cookie-popup';
    cookiePopup.innerHTML = `
        <p>Nous utilisons des cookies pour améliorer votre expérience. Acceptez-vous l'utilisation des cookies ?</p>
        <button id="accept-cookies">Accepter</button>
        <button id="decline-cookies">Refuser</button>
        <br>
        <a href="/politique-cookies" id="cookie-policy-link" target="_blank">En savoir plus</a>
    `;

    // Ajouter le style du popup
    Object.assign(cookiePopup.style, {
        position: 'fixed',
        bottom: '20px',
        left: '50%',
        transform: 'translateX(-50%)',
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        color: 'white',
        padding: '20px',
        borderRadius: '5px',
        zIndex: '1000',
        textAlign: 'center'
    });

    // Ajouter les styles aux boutons
    var buttons = cookiePopup.querySelectorAll('button');
    buttons.forEach(button => Object.assign(button.style, {
        backgroundColor: '#4CAF50', // Vert
        color: 'white',
        border: 'none',
        padding: '10px 20px',
        cursor: 'pointer',
        borderRadius: '5px',
        margin: '5px'
    }));

    // Ajouter le style du lien vers la politique des cookies
    var policyLink = cookiePopup.querySelector('#cookie-policy-link');
    Object.assign(policyLink.style, {
        color: '#FFD700', // Or
        textDecoration: 'underline',
        fontSize: '14px',
        display: 'block',
        marginTop: '10px'
    });

    // Afficher le popup
    document.body.appendChild(cookiePopup);

    // Fonction pour gérer le consentement aux cookies
    function handleConsent(value) {
        let maxAge = value === 'true' ? 60 * 60 * 24 * 365 : 60 * 60 * 24; // 1 an si accepté, 1 jour si refusé
        document.cookie = `cookie_consent=${value}; path=/; max-age=${maxAge}`;
        cookiePopup.style.display = 'none';
        location.reload(); // Rafraîchir la page après le choix
    }

    // Action si l'utilisateur accepte les cookies
    document.getElementById('accept-cookies').onclick = () => handleConsent('true');

    // Action si l'utilisateur refuse les cookies
    document.getElementById('decline-cookies').onclick = () => handleConsent('false');
}
