<?php
/** Consultant - Manage Blogs */
$pageTitle = $lang === 'hi' ? 'ब्लॉग प्रबंधित करें' : 'Manage Blogs';
$db = getDB();

// Handle new/edit blog
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRF($_POST['csrf_token'] ?? '')) {
    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($_POST['title'])));
    $slug = trim($slug, '-');
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $up = uploadFile($_FILES['image'], 'blogs', ALLOWED_IMAGE_TYPES);
        if ($up['success'])
            $image = $up['path'];
    }

    if (!empty($_POST['blog_id'])) {
        // Update
        $sql = "UPDATE blogs SET title=?, title_hi=?, slug=?, excerpt=?, excerpt_hi=?, content=?, content_hi=?, category_id=?, meta_description=?, status=?";
        $params = [$_POST['title'], $_POST['title_hi'] ?? '', $slug, $_POST['excerpt'] ?? '', $_POST['excerpt_hi'] ?? '', $_POST['content'], $_POST['content_hi'] ?? '', intval($_POST['category_id'] ?? 1), $_POST['meta_description'] ?? '', $_POST['status'] ?? 'draft'];
        if ($image) {
            $sql .= ", image=?";
            $params[] = $image;
        }
        $sql .= " WHERE id=?";
        $params[] = $_POST['blog_id'];
        if ($_POST['status'] === 'published') {
            $sql = str_replace(' WHERE', ', published_at=COALESCE(published_at,NOW()) WHERE', $sql);
        }
        $db->prepare($sql)->execute($params);
    } else {
        // Insert
        $db->prepare("INSERT INTO blogs (author_id,title,title_hi,slug,excerpt,excerpt_hi,content,content_hi,image,category_id,meta_description,status,published_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute([
            currentUserId(),
            $_POST['title'],
            $_POST['title_hi'] ?? '',
            $slug,
            $_POST['excerpt'] ?? '',
            $_POST['excerpt_hi'] ?? '',
            $_POST['content'],
            $_POST['content_hi'] ?? '',
            $image,
            intval($_POST['category_id'] ?? 1),
            $_POST['meta_description'] ?? '',
            $_POST['status'] ?? 'draft',
            $_POST['status'] === 'published' ? date('Y-m-d H:i:s') : null
        ]);
    }
    setFlash('success', $lang === 'hi' ? 'ब्लॉग सहेजा गया!' : 'Blog saved!');
    header('Location:' . BASE_URL . 'consultant/blogs');
    exit;
}

// Handle delete (POST + CSRF required)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && verifyCSRF($_POST['csrf_token'] ?? '')) {
    $db->prepare("DELETE FROM blogs WHERE id = ?")->execute([intval($_POST['delete_id'])]);
    setFlash('success', 'Deleted!');
    header('Location:' . BASE_URL . 'consultant/blogs');
    exit;
}

$blogs = $db->query("SELECT b.*, c.name as category_name FROM blogs b LEFT JOIN blog_categories c ON b.category_id = c.id ORDER BY b.created_at DESC")->fetchAll();
$categories = $db->query("SELECT * FROM blog_categories")->fetchAll();

$editBlog = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editBlog = $stmt->fetch();
}

require __DIR__ . '/../../layouts/consultant_header.php';
?>
<div class="dash-header flex-between">
    <div>
        <h1>
            <?= $pageTitle ?>
        </h1>
    </div>
    <a href="<?= BASE_URL ?>consultant/blogs?edit=new" class="btn btn-primary">
        <?= $lang === 'hi' ? 'नया ब्लॉग' : 'New Blog' ?> +
    </a>
</div>

<?php if (isset($_GET['edit'])): ?>
    <!-- Blog Editor -->
    <div class="card" style="padding:var(--space-6);">
        <h3 class="mb-6">
            <?= $editBlog ? ($lang === 'hi' ? 'ब्लॉग संपादित करें' : 'Edit Blog') : ($lang === 'hi' ? 'नया ब्लॉग' : 'New Blog') ?>
        </h3>
        <form method="POST" enctype="multipart/form-data">
            <?= csrfField() ?>
            <?php if ($editBlog): ?><input type="hidden" name="blog_id" value="<?= $editBlog['id'] ?>">
            <?php endif; ?>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Title (English) *</label><input type="text" name="title"
                        class="form-control" required value="<?= clean($editBlog['title'] ?? '') ?>"></div>
                <div class="form-group"><label class="form-label">शीर्षक (Hindi)</label><input type="text" name="title_hi"
                        class="form-control" value="<?= clean($editBlog['title_hi'] ?? '') ?>"></div>
            </div>
            <div class="grid grid-2">
                <div class="form-group"><label class="form-label">Excerpt (EN)</label><textarea name="excerpt"
                        class="form-control" rows="2"><?= clean($editBlog['excerpt'] ?? '') ?></textarea></div>
                <div class="form-group"><label class="form-label">सारांश (HI)</label><textarea name="excerpt_hi"
                        class="form-control" rows="2"><?= clean($editBlog['excerpt_hi'] ?? '') ?></textarea></div>
            </div>
            <!-- English Content Editor -->
            <div class="form-group" style="margin-bottom:var(--space-6);">
                <label class="form-label">Content (English) * <small style="color:var(--text-muted);font-weight:normal;">—
                        Use the editor below to format text, add images, and embed videos</small></label>
                <textarea name="content" id="editor-content"
                    class="tinymce-editor"><?= htmlspecialchars($editBlog['content'] ?? '') ?></textarea>
            </div>

            <!-- Hindi Content Editor -->
            <div class="form-group" style="margin-bottom:var(--space-6);">
                <label class="form-label">सामग्री (Hindi) <small style="color:var(--text-muted);font-weight:normal;">—
                        नीचे दिए गए एडिटर का उपयोग करें</small></label>
                <textarea name="content_hi" id="editor-content_hi"
                    class="tinymce-editor"><?= htmlspecialchars($editBlog['content_hi'] ?? '') ?></textarea>
            </div>
            <div class="grid grid-3">
                <div class="form-group"><label class="form-label">Category</label>
                    <select name="category_id" class="form-control">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($editBlog['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                <?= $c['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Image</label><input type="file" name="image"
                        class="form-control" accept="image/*">
                    <?php if ($editBlog && $editBlog['image']): ?><small class="text-muted">Current:
                            <?= basename($editBlog['image']) ?>
                        </small>
                    <?php endif; ?>
                </div>
                <div class="form-group"><label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="draft" <?= ($editBlog['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft
                        </option>
                        <option value="published" <?= ($editBlog['status'] ?? '') === 'published' ? 'selected' : '' ?>>
                            Published
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group"><label class="form-label">Meta Description (SEO)</label><input type="text"
                    name="meta_description" class="form-control" value="<?= clean($editBlog['meta_description'] ?? '') ?>">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    <?= t('save') ?> ✓
                </button>
                <a href="<?= BASE_URL ?>consultant/blogs" class="btn btn-outline btn-lg">
                    <?= t('cancel') ?>
                </a>
            </div>
        </form>
    </div>
<?php else: ?>
    <!-- Blog List -->
    <?php if (empty($blogs)): ?>
        <div class="card text-center" style="padding:var(--space-12);">
            <div style="font-size:3rem;">📰</div>
            <p class="text-muted mt-4">
                <?= t('no_results') ?>
            </p>
        </div>
    <?php else: ?>
        <div class="card" style="overflow:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blogs as $b): ?>
                        <tr>
                            <td><strong>
                                    <?= clean(mb_substr($b['title'], 0, 50)) ?>
                                </strong></td>
                            <td><span class="badge badge-info">
                                    <?= clean($b['category_name'] ?? '') ?>
                                </span></td>
                            <td><span class="badge badge-<?= $b['status'] === 'published' ? 'success' : 'warning' ?>">
                                    <?= $b['status'] ?>
                                </span></td>
                            <td class="text-muted">
                                <?= $b['views'] ?>
                            </td>
                            <td class="text-muted">
                                <?= $b['published_at'] ? date('d/m/Y', strtotime($b['published_at'])) : '-' ?>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="?edit=<?= $b['id'] ?>" class="btn btn-sm btn-outline">✏️</a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this blog?')">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="delete_id" value="<?= $b['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline"
                                            style="color:var(--accent);">🗑️</button>
                                    </form>
                                    <?php if ($b['status'] === 'published'): ?><a href="<?= BASE_URL ?>blog/<?= $b['slug'] ?>"
                                            target="_blank" class="btn btn-sm btn-outline">👁</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- TinyMCE Initialization -->
<script src="https://cdn.tiny.cloud/1/ojf8uvcorur9cf5p5wlk5l95knfblti8ni9zqyqawrvo0tfe/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
    tinymce.init({
        selector: '.tinymce-editor',
        height: 500,
        menubar: true,
        plugins: [
            // Core editing features
            'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
            // Your account includes a free trial of TinyMCE premium features
            // Try the most popular premium features until Mar 25, 2026:
            'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [
            { value: 'First.Name', title: 'First Name' },
            { value: 'Email', title: 'Email' },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
        content_style: 'body { font-family:Inter,sans-serif; font-size:16px; line-height:1.6 } img { max-width:100%; height:auto }',
        // Image upload handling
        images_upload_url: '<?= BASE_URL ?>api/upload_image.php',
        images_upload_credentials: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        // Inject CSRF token into image upload requests
        images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.withCredentials = true;
            xhr.open('POST', '<?= BASE_URL ?>api/upload_image.php');

            xhr.upload.onprogress = (e) => {
                progress(e.loaded / e.total * 100);
            };

            xhr.onload = () => {
                if (xhr.status === 403) {
                    reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                    return;
                }
                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }
                const json = JSON.parse(xhr.responseText);
                if (!json || typeof json.location != 'string') {
                    reject('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                resolve(json.location);
            };

            xhr.onerror = () => {
                reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };

            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>'); // Pass CSRF token

            xhr.send(formData);
        })
    });
</script>

<?php require __DIR__ . '/../../layouts/consultant_footer.php'; ?>