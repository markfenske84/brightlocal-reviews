/* BrightLocal Reviews – Admin (Vanilla JS)
------------------------------------------------------------------*/
// Utility helpers
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

/** Ajax helper using fetch + URLSearchParams */
const wpPost = (payload) => {
  return fetch(blReviewsAdmin.ajaxUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
    body: new URLSearchParams(payload),
  }).then((r) => r.json());
};

document.addEventListener('DOMContentLoaded', () => {
  /* ------------------------------------------------------------
   * Unsaved changes detection
   * ---------------------------------------------------------- */
  let unsavedAdditions = false;
  const initialWidgetIds = $$('.widget-row').map((row) => {
    const val = $('input[name$="[widget_id]"]', row).value.trim();
    return val || null;
  }).filter(Boolean);

  const markUnsaved = () => { unsavedAdditions = true; };

  // beforeunload
  window.addEventListener('beforeunload', (e) => {
    if (!unsavedAdditions) return;
    const msg = 'You have unsaved changes to your Widget IDs. If you leave this page, the changes will be lost.';
    e.preventDefault();
    e.returnValue = msg;
    return msg;
  });

  /* ------------------------------------------------------------
   * Add / remove widget rows
   * ---------------------------------------------------------- */
  $('#add-widget')?.addEventListener('click', () => {
    const tbody = $('#bl-widgets-table tbody');
    const index = $$('.widget-row', tbody).length;
    const tr = document.createElement('tr');
    tr.className = 'widget-row';
    tr.innerHTML = `
      <td><input type="text" name="bl_reviews_widgets[${index}][widget_id]" class="regular-text" placeholder="a1b2c3…"></td>
      <td><input type="text" name="bl_reviews_widgets[${index}][label]" class="regular-text" placeholder="e.g., Downtown Location"></td>
      <td><button type="button" class="button remove-widget">Remove</button></td>`;
    tbody.appendChild(tr);
    markUnsaved();
  });

  document.addEventListener('click', (e) => {
    if (e.target.closest('.remove-widget')) {
      e.target.closest('tr')?.remove();
      markUnsaved();
    }
  });

  document.addEventListener('input', (e) => {
    if (e.target.matches('input[name$="[widget_id]"]')) {
      const val = e.target.value.trim();
      if (val && !initialWidgetIds.includes(val)) markUnsaved();
    }
  });

  document.addEventListener('submit', () => { unsavedAdditions = false; });

  /* ------------------------------------------------------------
   * Action buttons visibility (delete/remove)
   * ---------------------------------------------------------- */
  const updateActionButtons = () => {
    const submitWrap = $('#bl_get_reviews')?.parentElement;
    if (!submitWrap) return;

    let holder = submitWrap.querySelector('.bl-action-buttons-wrapper');
    if (!holder) {
      holder = document.createElement('span');
      holder.className = 'bl-action-buttons-wrapper';
      submitWrap.appendChild(holder);
    }

    const hasReviews = $('#bl_get_reviews')?.dataset.hasReviews === '1';
    const hasSavedWidgets = initialWidgetIds.length > 0;

    // Delete reviews button
    if (hasReviews && !$('#bl_delete_all_reviews')) {
      const btn = document.createElement('button');
      btn.id = 'bl_delete_all_reviews';
      btn.type = 'button';
      btn.className = 'button button-link-delete';
      btn.style.marginRight = '10px';
      btn.textContent = 'Delete All Reviews';
      holder.appendChild(btn);
    }
    if (!hasReviews && $('#bl_delete_all_reviews')) {
      $('#bl_delete_all_reviews').remove();
    }

    // Remove widgets button
    if (hasSavedWidgets && !$('#bl_remove_all_widgets')) {
      const btn = document.createElement('button');
      btn.id = 'bl_remove_all_widgets';
      btn.type = 'button';
      btn.className = 'button button-link-delete';
      btn.textContent = 'Remove All Widgets';
      holder.appendChild(btn);
    }
    if (!hasSavedWidgets && $('#bl_remove_all_widgets')) {
      $('#bl_remove_all_widgets').remove();
    }

    if (!holder.children.length) holder.remove();
  };
  updateActionButtons();

  /* ------------------------------------------------------------
   * Save widgets & fetch reviews
   * ---------------------------------------------------------- */
  $('#bl_get_reviews')?.addEventListener('click', async (e) => {
    const btn = e.currentTarget;
    const rows = $$('.widget-row');
    const widgets = rows.map((row) => {
      return {
        widget_id: $('input[name$="[widget_id]"]', row).value.trim(),
        label: $('input[name$="[label]"]', row).value.trim(),
      };
    }).filter((w) => w.widget_id && w.label);

    if (!widgets.length) {
      alert('Please enter at least one widget ID before getting or updating reviews');
      return;
    }

    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Saving settings…';

    try {
      const saveRes = await wpPost({
        action: 'bl_save_widgets',
        nonce: blReviewsAdmin.saveWidgetsNonce,
        widgets: JSON.stringify(widgets),
      });
      if (!saveRes.success) throw saveRes.data || 'Error saving settings';

      unsavedAdditions = false;
      initialWidgetIds.length = 0;
      widgets.forEach((w) => initialWidgetIds.push(w.widget_id));

      btn.textContent = 'Fetching reviews…';
      const fetchRes = await wpPost({
        action: 'bl_get_reviews',
        nonce: blReviewsAdmin.nonce,
        widgets: JSON.stringify(widgets),
      });
      if (!fetchRes.success) throw fetchRes.data || 'Error fetching reviews';

      alert(fetchRes.data.message);
      btn.dataset.hasReviews = '1';
      btn.textContent = 'Update Reviews';
      updateActionButtons();
    } catch (err) {
      alert('Error: ' + err);
      btn.textContent = originalText;
    } finally {
      btn.disabled = false;
    }
  });

  /* ------------------------------------------------------------
   * Delete all reviews
   * ---------------------------------------------------------- */
  document.addEventListener('click', async (e) => {
    if (!e.target.matches('#bl_delete_all_reviews')) return;
    if (!confirm(blReviewsAdmin.confirmDelete)) return;

    const btn = e.target;
    btn.disabled = true;
    btn.textContent = 'Deleting reviews…';

    try {
      const res = await wpPost({ action: 'bl_delete_all_reviews', nonce: blReviewsAdmin.deleteNonce });
      if (!res.success) throw res.data || 'Error';
      alert(res.data.message);
      $('#bl_get_reviews').dataset.hasReviews = '0';
      updateActionButtons();
    } catch (err) {
      alert('Error: ' + err);
    } finally {
      btn.disabled = false;
      btn.textContent = 'Delete All Reviews';
    }
  });

  /* ------------------------------------------------------------
   * Remove all widgets
   * ---------------------------------------------------------- */
  document.addEventListener('click', async (e) => {
    if (!e.target.matches('#bl_remove_all_widgets')) return;
    if (!confirm(blReviewsAdmin.confirmRemoveWidgets)) return;

    const btn = e.target;
    btn.disabled = true;
    btn.textContent = 'Removing widgets…';

    try {
      const res = await wpPost({ action: 'bl_remove_all_widgets', nonce: blReviewsAdmin.removeWidgetsNonce });
      if (!res.success) throw res.data || 'Error';
      alert(res.data.message);
      location.reload();
    } catch (err) {
      alert('Error: ' + err);
      btn.disabled = false;
      btn.textContent = 'Remove All Widgets';
    }
  });

  /* ------------------------------------------------------------
   * Radius linking UI
   * ---------------------------------------------------------- */
  const syncRadius = (val) => {
    if (!$('#bl_link_radius')?.checked) return;
    ['radius_tr', 'radius_br', 'radius_bl'].forEach((id) => {
      const el = $('#' + id);
      if (el) el.value = val;
    });
  };

  const updateRadiusUI = () => {
    const linked = $('#bl_link_radius')?.checked;
    ['radius_tr', 'radius_br', 'radius_bl'].forEach((id) => {
      const input = $('#' + id);
      if (!input) return;
      const label = input.closest('label');
      if (linked) {
        input.setAttribute('readonly', 'readonly');
        label?.classList.add('hidden');
      } else {
        input.removeAttribute('readonly');
        label?.classList.remove('hidden');
      }
    });
    const btn = $('#bl_link_radius_btn');
    if (btn) {
      btn.setAttribute('aria-pressed', linked ? 'true' : 'false');
      const icon = btn.querySelector('.dashicons');
      if (icon) icon.className = linked ? 'dashicons dashicons-editor-unlink' : 'dashicons dashicons-admin-links';
    }
    if (linked) syncRadius($('#radius_tl')?.value || 0);
  };

  $('#bl_link_radius_btn')?.addEventListener('click', (e) => {
    e.preventDefault();
    const chk = $('#bl_link_radius');
    if (!chk) return;
    chk.checked = !chk.checked;
    updateRadiusUI();
  });

  $('#bl_link_radius')?.addEventListener('change', updateRadiusUI);
  $('#radius_tl')?.addEventListener('input', (e) => syncRadius(e.target.value));

  updateRadiusUI();
}); 