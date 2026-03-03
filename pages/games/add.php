<?php
session_start();
$pageTitle = "Ajouter un jeu — Admin";

// TODO: vérifier que l'utilisateur est admin
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: /index.php');
//     exit;
// }

include __DIR__ . '/../../includes/header.php';
?>

<div class="max-w-2xl mx-auto px-4 py-10">
    <div class="flex items-center gap-3 mb-2">
        <h1 class="font-tft text-3xl font-bold text-gold">Ajouter un jeu</h1>
        <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Admin</span>
    </div>
    <p class="text-gray-500 mb-8">Ajoutez un nouveau jeu au catalogue</p>

    <form action="" method="POST" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gold-light mb-1">Nom du jeu</label>
            <input type="text" id="name" name="name" required placeholder="Ex: Elden Ring, Minecraft..."
                   class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
        </div>
        <div>
            <label for="type" class="block text-sm font-medium text-gold-light mb-1">Genre</label>
            <input type="text" id="type" name="type" required placeholder="Ex: RPG / Action, Sandbox / Survie..."
                   class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gold-light mb-1">Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Décrivez ce jeu..."
                      class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all"></textarea>
        </div>
        <div>
            <label for="image" class="block text-sm font-medium text-gold-light mb-1">URL de l'image</label>
            <input type="url" id="image" name="image" placeholder="https://..."
                   class="w-full bg-tft-card-deep border border-tft-border rounded-lg px-4 py-3 text-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all">
        </div>
        <button type="submit"
                class="w-full bg-linear-to-br from-gold to-gold-dark text-tft-dark font-tft font-bold tracking-wide py-3 rounded-lg text-lg hover:from-gold-light hover:to-gold hover:shadow-[0_0_20px_rgba(254,137,94,0.5)] transition-all">
            ➕ Ajouter ce jeu
        </button>
    </form>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
