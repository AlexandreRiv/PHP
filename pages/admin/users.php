<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
secureSessionStart();
requireAdmin();

$pageTitle = 'Utilisateurs — Admin';
$db = getDB();
$users = $db->query('SELECT * FROM user ORDER BY created_at DESC')->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

    <div class="max-w-6xl mx-auto px-4 py-12">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="font-tft text-3xl font-bold text-gold mb-1">Utilisateurs</h1>
                <p class="text-gray-400"><?= count($users) ?> compte<?= count($users) > 1 ? 's' : '' ?>
                    inscrit<?= count($users) > 1 ? 's' : '' ?></p>
            </div>
            <a href="/pages/admin/dashboard.php"
               class="text-gray-400 hover:text-gold transition text-sm">← Dashboard</a>
        </div>

        <div class="bg-linear-to-br from-tft-card to-tft-card-deep border border-tft-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                    <tr class="border-b border-tft-border text-left">
                        <th class="px-6 py-4 text-gold font-tft font-semibold">#</th>
                        <th class="px-6 py-4 text-gold font-tft font-semibold">Utilisateur</th>
                        <th class="px-6 py-4 text-gold font-tft font-semibold">Email</th>
                        <th class="px-6 py-4 text-gold font-tft font-semibold">Rôle</th>
                        <th class="px-6 py-4 text-gold font-tft font-semibold">Inscrit le</th>
                        <th class="px-6 py-4 text-gold font-tft font-semibold">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-tft-border">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-tft-dark/30 transition">
                            <td class="px-6 py-4 text-gray-500"><?= $u['id'] ?></td>
                            <td class="px-6 py-4 text-gray-200 font-medium"><?= e($u['username']) ?></td>
                            <td class="px-6 py-4 text-gray-400"><?= e($u['email']) ?></td>
                            <td class="px-6 py-4">
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="bg-linear-to-br from-gold to-gold-dark text-tft-dark text-xs font-bold px-2 py-0.5 rounded-full">Admin</span>
                                <?php else: ?>
                                    <span class="bg-[#96527A]/50 text-[#F99F72] text-xs px-2 py-0.5 rounded-full">Joueur</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td class="px-6 py-4">
                                <div class="flex gap-3 items-center">
                                    <a href="/pages/admin/edit_user.php?id=<?= $u['id'] ?>"
                                       class="text-gold hover:text-gold-light transition flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Editer</a>
                                    <?php if ($u['id'] !== $_SESSION['user']['id']): ?>
                                        <form action="/pages/admin/delete_user.php" method="POST" class="inline"
                                              onsubmit="return confirm('Supprimer <?= e($u['username']) ?> ?')">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <button type="submit"
                                                    class="text-red-400 hover:text-red-300 transition cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>