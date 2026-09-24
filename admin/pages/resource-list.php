<section class="admin-page-heading">
    <div>
        <p class="admin-eyebrow">Content library</p>
        <h1><?php echo tamim_e($resource['plural']); ?></h1>
        <p class="admin-page-intro">Organize the <?php echo tamim_e(mb_strtolower($resource['singular'])); ?> items visitors see on the website.</p>
    </div>
    <div class="admin-heading-actions">
        <a class="admin-button admin-button-primary" href="<?php echo tamim_e(admin_url($resourceName . '/new')); ?>">Add <?php echo tamim_e($resource['singular']); ?> <span>+</span></a>
    </div>
</section>

<section class="admin-panel">
    <div class="admin-panel-heading">
        <div><p class="admin-eyebrow">Library</p><h2>All <?php echo tamim_e($resource['plural']); ?></h2></div>
        <span class="admin-panel-count"><?php echo (int) $recordCount; ?> total</span>
    </div>
    <?php if ($records === []): ?>
        <div class="admin-empty-state">
            <span class="admin-empty-mark"><?php echo tamim_e(mb_strtoupper(mb_substr($resource['singular'], 0, 1))); ?></span>
            <h2>No <?php echo tamim_e(mb_strtolower($resource['plural'])); ?> yet</h2>
            <p>Add the first item to start building this part of the website.</p>
            <a class="admin-button admin-button-primary" href="<?php echo tamim_e(admin_url($resourceName . '/new')); ?>">Create <?php echo tamim_e($resource['singular']); ?> <span>+</span></a>
        </div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <?php foreach ($resource['columns'] as $column): ?><th><?php echo tamim_e($column['label']); ?></th><?php endforeach; ?>
                        <th><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <?php foreach ($resource['columns'] as $column): ?>
                                <?php $columnField = current(array_filter($resource['fields'], static fn (array $field): bool => $field['name'] === $column['key'])); ?>
                                <td><?php echo tamim_e(admin_format_list_value($columnField ?: ['type' => 'text'], $record[$column['key']] ?? null)); ?></td>
                            <?php endforeach; ?>
                            <td>
                                <div class="admin-row-actions">
                                    <a class="admin-icon-button" href="<?php echo tamim_e(admin_url($resourceName . '/edit?id=' . (int) $record['id'])); ?>">Edit</a>
                                    <form method="post" action="<?php echo tamim_e(admin_url($resourceName . '/delete')); ?>">
                                        <?php echo tamim_csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo (int) $record['id']; ?>">
                                        <button class="admin-icon-button admin-icon-button-danger" type="submit" onclick="return confirm('Delete this <?php echo tamim_e(mb_strtolower($resource['singular'])); ?>?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pageCount > 1): ?>
            <nav class="admin-pagination" aria-label="Pagination">
                <?php if ($page > 1): ?><a class="admin-button admin-button-secondary" href="?page=<?php echo (int) $page - 1; ?>">Previous</a><?php endif; ?>
                <span>Page <?php echo (int) $page; ?> of <?php echo (int) $pageCount; ?></span>
                <?php if ($page < $pageCount): ?><a class="admin-button admin-button-secondary" href="?page=<?php echo (int) $page + 1; ?>">Next</a><?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</section>
