// Fonction pour marquer une notification comme lue
function markNotificationAsRead(notifId) {
    // Envoyer la requête AJAX pour marquer la notification comme lue
    fetch('https://family.matthieudevilliers.fr/scripts/mark-notif-read/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notif_id=' + notifId
    })
    .then(response => {
        // Vérifier que la réponse est OK avant de parser le JSON
        if (!response.ok) {
            throw new Error('Erreur HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Fermer le toast
            const toastElement = document.getElementById('toast-' + notifId);
            if (toastElement) {
                const toast = bootstrap.Toast.getInstance(toastElement);
                if (toast) {
                    toast.hide();
                }
            }
        } else {
            console.error('Erreur lors de la mise à jour de la notification:', data.message);
        }
    })
    .catch(error => {
        console.error('Erreur réseau:', error);
    });
}

// Initialiser les toasts Bootstrap au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const toastElements = document.querySelectorAll('.toast');
    toastElements.forEach(function(toastElement) {
        // Créer une instance Bootstrap Toast pour chaque élément
        new bootstrap.Toast(toastElement, {
            autohide: false // Ne pas masquer automatiquement
        });
    });
    
    // Attacher les gestionnaires d'événements de fermeture aux boutons
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('notif-close-btn')) {
            const toastElement = event.target.closest('.toast');
            if (toastElement) {
                const notifId = toastElement.getAttribute('data-notif-id');
                if (notifId) {
                    markNotificationAsRead(parseInt(notifId, 10));
                }
            }
        }
    });
});
