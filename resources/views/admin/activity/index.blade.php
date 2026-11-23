@extends('layouts.app')

@section('content')
<style>
    .activity-page { color: #e2e8f0; }
    .activity-header, .activity-panel {
        border: 1px solid rgba(148, 163, 184, .16);
        border-radius: 1rem;
        background: rgba(15, 23, 42, .88);
        box-shadow: 0 14px 36px rgba(2, 6, 23, .18);
        overflow: hidden;
    }
    .activity-header { padding: 1.15rem 1.35rem; margin-bottom: 1rem; }
    .activity-panel { height: 100%; }
    .activity-panel-heading {
        display: flex; align-items: center; justify-content: space-between; gap: .75rem;
        padding: 1rem 1.1rem; background: rgba(30, 41, 59, .76);
        border-bottom: 1px solid rgba(148, 163, 184, .14);
    }
    .activity-panel-body { padding: .85rem 1rem; max-height: 72vh; overflow-y: auto; }
    #online-users.activity-panel-body { padding: .7rem .8rem; }
    .activity-page .min-w-0 { min-width: 0; }
    .activity-user, .activity-entry {
        display: flex; align-items: flex-start; gap: .8rem;
        padding: .85rem .25rem; border-bottom: 1px solid rgba(148, 163, 184, .12);
    }
    .activity-user { align-items: center; gap: .7rem; }
    .activity-user:last-child, .activity-entry:last-child { border-bottom: 0; }
    .activity-user-button {
        width: 100%; padding: .7rem .65rem; color: inherit; font: inherit; text-align: left; cursor: pointer; background: transparent; border: 0;
        border-radius: .75rem; transition: background-color .16s ease, box-shadow .16s ease;
    }
    .activity-user-button:hover, .activity-user-button:focus-visible {
        background: rgba(59, 130, 246, .10); outline: none;
    }
    .activity-user-button.is-selected {
        background: rgba(37, 99, 235, .18); box-shadow: inset 2px 0 #3b82f6;
    }
    .activity-filter {
        display: flex; justify-content: space-between; align-items: center; gap: .75rem;
        padding: .65rem .75rem; margin: 0 0 .55rem; border: 1px solid rgba(59, 130, 246, .24);
        border-radius: .7rem; color: #cbd5e1; background: rgba(37, 99, 235, .09); font-size: .8rem;
    }
    .activity-avatar {
        display: grid; place-items: center; flex: 0 0 2.35rem; height: 2.35rem;
        border-radius: .75rem; color: #93c5fd; background: rgba(37, 99, 235, .18);
    }
    .activity-online-dot { width: .5rem; height: .5rem; flex: 0 0 .5rem; border-radius: 50%; background: #22c55e; box-shadow: 0 0 0 2px rgba(34, 197, 94, .16); }
    .activity-user-button .activity-online-dot { margin: 0 .25rem 0 .15rem; }
    .activity-muted { color: #94a3b8; font-size: .78rem; }
    .activity-count, .activity-method {
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid rgba(148, 163, 184, .24); border-radius: 999px;
        color: #dbeafe; background: rgba(30, 41, 59, .92);
        font-weight: 600; line-height: 1.2; white-space: nowrap;
    }
    .activity-count {
        min-width: 1.8rem; min-height: 1.8rem; padding: .25rem .55rem;
        color: #86efac; border-color: rgba(34, 197, 94, .32); background: rgba(20, 83, 45, .32);
        font-size: .75rem;
    }
    .activity-method { min-width: 3.2rem; padding: .35rem .55rem; text-align: center; font-size: .65rem; letter-spacing: .04em; }
    .activity-empty { padding: 2rem 1rem; color: #94a3b8; text-align: center; }
    @media (max-width: 767.98px) { .activity-header { padding: 1rem; } }
</style>

<div class="container-fluid activity-page py-3">
    <section class="activity-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="activity-avatar" style="width:3rem;height:3rem;font-size:1.15rem"><i class="fas fa-user-clock"></i></div>
            <div>
                <h1 class="h5 text-white mb-1">Aktivitas Pengguna</h1>
                <div class="activity-muted">Pantau pengguna yang sedang aktif dan aktivitas terbaru di sistem.</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 activity-muted"><span class="activity-online-dot"></span> Diperbarui otomatis</div>
    </section>

    <div class="row g-3">
        <div class="col-xl-4">
            <section class="activity-panel">
                <header class="activity-panel-heading">
                    <div><h2 class="h6 text-white mb-1">Pengguna Online</h2><div class="activity-muted">Aktif dalam 90 detik terakhir</div></div>
                    <span class="activity-count" id="online-count">0</span>
                </header>
                <div class="activity-panel-body" id="online-users" aria-live="polite">
                    <div class="activity-empty">Memuat status pengguna…</div>
                </div>
            </section>
        </div>
        <div class="col-xl-8">
            <section class="activity-panel">
                <header class="activity-panel-heading">
                    <div><h2 class="h6 text-white mb-1">Aktivitas Terbaru</h2><div class="activity-muted">Catatan aksi pengguna, diperbarui otomatis</div></div>
                    <span class="activity-muted" id="feed-updated">Menunggu data</span>
                </header>
                <div class="activity-panel-body" aria-live="polite">
                    <div class="activity-filter" id="activity-filter" hidden>
                        <span>Memantau aktivitas <strong class="text-white" id="selected-user-name"></strong></span>
                        <button type="button" class="btn btn-sm btn-outline-light" id="clear-user-filter">Semua pengguna</button>
                    </div>
                    <div id="activity-feed">
                    <div class="activity-empty">Memuat aktivitas…</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const endpoint = @json(route('admin.activity.feed'));
    const onlineContainer = document.getElementById('online-users');
    const activityContainer = document.getElementById('activity-feed');
    const onlineCount = document.getElementById('online-count');
    const updatedLabel = document.getElementById('feed-updated');
    const filterBar = document.getElementById('activity-filter');
    const selectedUserName = document.getElementById('selected-user-name');
    const clearFilterButton = document.getElementById('clear-user-filter');
    let displayTimezone = 'Asia/Makassar';
    let selectedUserId = null;
    let selectedUserLabel = '';
    let latestRequestSequence = 0;

    const addText = (parent, tag, className, value) => {
        const node = document.createElement(tag);
        if (className) node.className = className;
        node.textContent = value || '';
        parent.appendChild(node);
        return node;
    };

    const formatDate = (value) => {
        if (!value) return '—';
        return new Intl.DateTimeFormat('id-ID', {
            dateStyle: 'medium', timeStyle: 'short', timeZone: displayTimezone
        }).format(new Date(value));
    };

    const renderOnline = (users) => {
        onlineCount.textContent = users.length;
        onlineContainer.replaceChildren();
        if (!users.length) {
            addText(onlineContainer, 'div', 'activity-empty', 'Belum ada pengguna yang terdeteksi online.');
            return;
        }
        users.forEach((user) => {
            const row = document.createElement('button');
            row.type = 'button';
            row.className = `activity-user activity-user-button${String(user.id) === String(selectedUserId) ? ' is-selected' : ''}`;
            row.setAttribute('aria-pressed', String(String(user.id) === String(selectedUserId)));
            row.setAttribute('aria-label', `Pantau aktivitas ${user.name}`);
            row.addEventListener('click', () => {
                selectedUserId = user.id;
                selectedUserLabel = user.name;
                refresh();
            });
            const avatar = document.createElement('div'); avatar.className = 'activity-avatar';
            const icon = document.createElement('i'); icon.className = 'fas fa-user'; avatar.appendChild(icon); row.appendChild(avatar);
            const details = document.createElement('div'); details.className = 'flex-grow-1 min-w-0';
            addText(details, 'div', 'text-white fw-semibold text-truncate', user.name);
            addText(details, 'div', 'activity-muted text-capitalize', [user.role, user.department].filter(Boolean).join(' · '));
            row.appendChild(details);
            const dot = document.createElement('span'); dot.className = 'activity-online-dot'; dot.title = `Aktif ${formatDate(user.last_seen_at)}`; row.appendChild(dot);
            onlineContainer.appendChild(row);
        });
    };

    const renderActivities = (activities) => {
        activityContainer.replaceChildren();
        if (!activities.length) {
            addText(activityContainer, 'div', 'activity-empty', 'Belum ada aktivitas yang tercatat.');
            return;
        }
        activities.forEach((item) => {
            const row = document.createElement('div'); row.className = 'activity-entry';
            const method = addText(row, 'span', 'activity-method', item.method);
            method.setAttribute('aria-label', `Metode ${item.method}`);
            const details = document.createElement('div'); details.className = 'flex-grow-1 min-w-0';
            const title = document.createElement('div'); title.className = 'text-white';
            const actor = document.createElement('strong'); actor.textContent = item.actor; title.appendChild(actor);
            title.appendChild(document.createTextNode(` — ${item.activity}`));
            details.appendChild(title);
            addText(details, 'div', 'activity-muted', [item.role, item.department].filter(Boolean).join(' · ') || 'Pengguna');
            row.appendChild(details);
            addText(row, 'time', 'activity-muted text-nowrap', formatDate(item.occurred_at));
            activityContainer.appendChild(row);
        });
    };

    const refresh = async () => {
        if (document.visibilityState !== 'visible') return;
        const requestSequence = ++latestRequestSequence;
        try {
            const url = new URL(endpoint, window.location.origin);
            if (selectedUserId !== null) url.searchParams.set('user_id', selectedUserId);
            const response = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Gagal memuat aktivitas');
            const data = await response.json();
            if (requestSequence !== latestRequestSequence) return;
            displayTimezone = data.timezone || displayTimezone;
            filterBar.hidden = selectedUserId === null;
            selectedUserName.textContent = selectedUserLabel;
            renderOnline(data.online || []);
            renderActivities(data.activities || []);
            updatedLabel.textContent = `Diperbarui ${formatDate(data.updated_at)}`;
        } catch (error) {
            updatedLabel.textContent = 'Gagal memperbarui data';
        }
    };

    clearFilterButton.addEventListener('click', () => {
        selectedUserId = null;
        selectedUserLabel = '';
        refresh();
    });

    refresh();
    window.setInterval(refresh, 15000);
    document.addEventListener('visibilitychange', refresh);
});
</script>
@endsection
