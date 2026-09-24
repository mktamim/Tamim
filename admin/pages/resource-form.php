<section class="admin-page-heading">
    <div>
        <p class="admin-eyebrow"><?php echo tamim_e($resource['singular']); ?></p>
        <h1><?php echo tamim_e($editing ? 'Edit ' : 'Create ') . tamim_e($resource['singular']); ?></h1>
        <p class="admin-page-intro"><?php echo tamim_e($editing ? 'Update the details shown on the public website.' : 'Add a new item to the public website.'); ?></p>
    </div>
    <div class="admin-heading-actions">
        <a class="admin-button admin-button-secondary" href="<?php echo tamim_e(admin_url($resourceName)); ?>">Cancel <span>×</span></a>
    </div>
</section>

<section class="admin-panel">
    <form class="admin-form admin-form-grid" method="post" enctype="<?php echo $hasImageFields ? 'multipart/form-data' : 'application/x-www-form-urlencoded'; ?>" action="">
        <?php echo tamim_csrf_field(); ?>
        <?php if (isset($formErrors['form'])): ?>
            <div class="admin-notice admin-notice-error admin-form-full" role="alert"><?php echo tamim_e($formErrors['form']); ?></div>
        <?php endif; ?>
        <?php foreach ($resource['fields'] as $field): ?>
            <?php
            $fieldName = $field['name'];
            $fieldValue = $formValues[$fieldName] ?? '';
            $fieldError = $formErrors[$fieldName] ?? '';
            $fieldClass = 'admin-form-field';
            if ($field['type'] === 'textarea' || $field['type'] === 'richtext' || $field['type'] === 'json') {
                $fieldClass .= ' admin-form-full';
            }
            if ($field['type'] === 'image') {
                $fieldClass .= ' admin-form-full';
            }
            ?>
            <div class="<?php echo tamim_e($fieldClass); ?>">
                <?php if ($field['type'] === 'boolean'): ?>
                    <label class="admin-check-row">
                        <input type="checkbox" name="<?php echo tamim_e($fieldName); ?>" value="1"<?php echo (int) $fieldValue === 1 ? ' checked' : ''; ?>>
                        <span><?php echo tamim_e($field['label']); ?></span>
                    </label>
                <?php elseif ($field['type'] === 'image'): ?>
                    <span class="admin-field-label"><?php echo tamim_e($field['label']); ?></span>
                    <?php if ($fieldValue !== '' && $fieldValue !== null): ?>
                        <div class="admin-image-preview">
                            <img src="/<?php echo tamim_e(ltrim((string) $fieldValue, '/')); ?>" alt="">
                            <label class="admin-check-row"><input type="checkbox" name="remove_<?php echo tamim_e($fieldName); ?>" value="1"> Remove current image</label>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="<?php echo tamim_e($fieldName); ?>" accept="<?php echo tamim_e($field['accept'] ?? 'image/*'); ?>">
                    <small>JPG, PNG or WEBP. Maximum 2 MB and 4000 × 4000 pixels.</small>
                <?php elseif ($field['type'] === 'textarea' || $field['type'] === 'richtext' || $field['type'] === 'json'): ?>
                    <label>
                        <span><?php echo tamim_e($field['label']); ?><?php if ($field['type'] === 'json'): ?><small class="admin-label-note">One item per line</small><?php endif; ?></span>
                        <textarea name="<?php echo tamim_e($fieldName); ?>" rows="<?php echo $field['type'] === 'richtext' ? 10 : 6; ?>"<?php echo ($field['required'] ?? false) ? ' required' : ''; ?><?php echo isset($field['max']) ? ' maxlength="' . (int) $field['max'] . '"' : ''; ?>><?php echo tamim_e(admin_field_value($field, $fieldValue)); ?></textarea>
                        <?php if ($field['type'] === 'richtext'): ?><small>Basic HTML tags are allowed and sanitized.</small><?php endif; ?>
                    </label>
                <?php elseif ($field['type'] === 'select'): ?>
                    <label>
                        <span><?php echo tamim_e($field['label']); ?></span>
                        <select name="<?php echo tamim_e($fieldName); ?>" required>
                            <?php foreach ($field['options'] as $optionValue => $optionLabel): ?>
                                <option value="<?php echo tamim_e($optionValue); ?>"<?php echo (string) $fieldValue === (string) $optionValue ? ' selected' : ''; ?>><?php echo tamim_e($optionLabel); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                <?php elseif ($field['type'] === 'date'): ?>
                    <label>
                        <span><?php echo tamim_e($field['label']); ?></span>
                        <input type="date" name="<?php echo tamim_e($fieldName); ?>" value="<?php echo tamim_e(admin_field_value($field, $fieldValue)); ?>"<?php echo ($field['required'] ?? false) ? ' required' : ''; ?>>
                    </label>
                <?php elseif ($field['type'] === 'datetime'): ?>
                    <label>
                        <span><?php echo tamim_e($field['label']); ?></span>
                        <input type="datetime-local" name="<?php echo tamim_e($fieldName); ?>" value="<?php echo tamim_e(admin_field_value($field, $fieldValue)); ?>">
                    </label>
                <?php elseif ($field['type'] === 'integer' || $field['type'] === 'year'): ?>
                    <label>
                        <span><?php echo tamim_e($field['label']); ?></span>
                        <input type="number" name="<?php echo tamim_e($fieldName); ?>" value="<?php echo tamim_e(admin_field_value($field, $fieldValue)); ?>"<?php echo ($field['required'] ?? false) ? ' required' : ''; ?> min="<?php echo (int) ($field['min'] ?? ($field['type'] === 'year' ? 1900 : 0)); ?>" max="<?php echo (int) ($field['max_value'] ?? ($field['type'] === 'year' ? 2100 : 9999)); ?>">
                    </label>
                <?php else: ?>
                    <label>
                        <span><?php echo tamim_e($field['label']); ?></span>
                        <input type="<?php echo $field['type'] === 'email' ? 'email' : ($field['type'] === 'url' ? 'url' : 'text'); ?>" name="<?php echo tamim_e($fieldName); ?>" value="<?php echo tamim_e(admin_field_value($field, $fieldValue)); ?>"<?php echo ($field['required'] ?? false) ? ' required' : ''; ?><?php echo isset($field['max']) ? ' maxlength="' . (int) $field['max'] . '"' : ''; ?><?php echo $fieldName === 'slug' ? ' data-slug-source="title"' : ''; ?>>
                    </label>
                <?php endif; ?>
                <?php if ($fieldError !== ''): ?><p class="admin-field-error"><?php echo tamim_e($fieldError); ?></p><?php endif; ?>
            </div>
        <?php endforeach; ?>
        <div class="admin-form-actions admin-form-full">
            <button class="admin-button admin-button-primary" type="submit"><?php echo tamim_e($editing ? 'Save changes' : 'Create ' . $resource['singular']); ?> <span><?php echo $editing ? '✓' : '+'; ?></span></button>
            <a class="admin-button admin-button-secondary" href="<?php echo tamim_e(admin_url($resourceName)); ?>">Cancel</a>
        </div>
    </form>
</section>
<script>
(function () {
    document.querySelectorAll('[data-slug-source]').forEach(function (slugInput) {
        var titleInput = document.querySelector('input[name="title"]');
        if (!titleInput || slugInput.value) {
            return;
        }
        titleInput.addEventListener('input', function () {
            if (slugInput.value || !titleInput.value) {
                return;
            }
            slugInput.value = titleInput.value.toLowerCase().normalize('NFKD').replace(/[^\w\s-]/g, '').trim().replace(/[\s_]+/g, '-').replace(/-+/g, '-');
        });
    });
}());
</script>
