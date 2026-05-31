// Gestion des filtres de marques et modèles
const filtres = document.querySelectorAll('.zoneFiltres input[type="checkbox"]');
const mainContainer = document.querySelector('main');

// On vérifie qu'on est bien sur la page qui contient les filtres
if (filtres.length > 0 && mainContainer) {
    
    // 1. Au chargement, on coche tout et on ajoute les classes au main 
    filtres.forEach(input => {
        input.checked = true;
        mainContainer.classList.add(input.name); 
    });

    // 2. Quand un input change, on ajoute ou retire la classe sur le main
    filtres.forEach(input => {
        input.addEventListener('change', function() {
            // Le deuxième paramètre "this.checked" force l'ajout si vrai, ou le retrait si faux
            mainContainer.classList.toggle(this.name, this.checked);
        });
    });
}