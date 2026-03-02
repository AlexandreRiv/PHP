<?php
session_start();
$pageTitle = "PHP Cest trop bien!";

include __DIR__ . '/../../includes/header.php';
?>

<div class="max-w-2xl mx-auto px-4 py-10">
    <h1 class="font-tft text-3xl font-bold text-gold mb-2">⚔️ Recruter un Champion</h1>
    <p class="text-gray-500 mb-8">Ajoutez un nouveau champion à votre plateau</p>

    <form action="" method="POST" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gold-light mb-1">Nom du champion</label>
            <input type="text" id="name" name="name" required placeholder="Ex: Yasuo, Jinx..."
                   class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
        </div>
        <div>
            <label for="type" class="block text-sm font-medium text-gold-light mb-1">Trait / Classe</label>
            <input type="text" id="type" name="type" required placeholder="Ex: Duelliste, Assassin..."
                   class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gold-light mb-1">Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Décrivez ce champion..."
                      class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all"></textarea>
        </div>
        <div>
            <label for="image" class="block text-sm font-medium text-gold-light mb-1">URL de l'image</label>
            <input type="url" id="image" name="image" placeholder="https://..."
                   class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
        </div>
        <button type="submit"
                class="w-full bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold tracking-wide py-3 rounded-lg text-lg hover:from-gold-light hover:to-gold hover:shadow-[0_0_20px_rgba(240,178,50,0.5)] transition-all">
            🛡️ Recruter ce Champion
        </button>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
