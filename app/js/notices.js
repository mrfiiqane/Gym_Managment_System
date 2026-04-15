$(document).ready(function() {
    loadNotices();

    function loadNotices() {
        $.ajax({
            url: "../api/notices.php",
            method: "POST",
            data: { action: "read_all" },
            dataType: "json",
            beforeSend: function () {
              showLoader(2);
            },
            success: function(response) {
                if (response.status) {
                    let html = "";
                    if (response.data.length === 0) {
                        html = `
                            <div class="col-span-full py-20 flex flex-col items-center justify-center text-slate-400">
                                <div class="w-20 h-20 bg-slate-100 dark:bg-white/5 rounded-full flex items-center justify-center mb-4">
                                    <span class="material-symbols-outlined text-4xl">campaign</span>
                                </div>
                                <p class="font-medium">No announcements yet.</p>
                            </div>
                        `;
                    } else {
                        response.data.forEach(notice => {
                            let date = new Date(notice.created_at).toLocaleDateString('en-GB', {
                                day: 'numeric', month: 'short', year: 'numeric'
                            });
                            
                            let expiryDate = notice.expires_at ? new Date(notice.expires_at).toLocaleDateString('en-GB', {
                                day: 'numeric', month: 'short', year: 'numeric'
                            }) : null;

                            const categoryColors = {
                                'Announcement': 'bg-blue-100 text-blue-600 dark:bg-blue-500/10',
                                'Holiday': 'bg-red-100 text-red-600 dark:bg-red-500/10',
                                'Event': 'bg-purple-100 text-purple-600 dark:bg-purple-500/10',
                                'Update': 'bg-green-100 text-green-600 dark:bg-green-500/10'
                            };

                            let showDelete = (currentRole === 'Admin' || notice.posted_by == currentUserId);
                            let deleteBtn = showDelete ? `
                                <button onclick="deleteNotice(${notice.id})" class="text-red-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                    Delete
                                </button>
                            ` : "";
                            
                            html += `
                                <div class="bg-white dark:bg-darkPanel p-8 rounded-[2.5rem] shadow-sm border border-primary/5 hover:shadow-xl transition-all group">
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="p-3 ${categoryColors[notice.category] || 'bg-primary/10 text-primary'} rounded-2xl">
                                            <span class="material-symbols-outlined">campaign</span>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">${date}</span>
                                            <span class="px-3 py-1 mt-1 rounded-full text-[9px] font-black uppercase ${categoryColors[notice.category] || 'bg-slate-100 text-slate-600'}">${notice.category}</span>
                                        </div>
                                    </div>
                                    
                                    <h3 class="text-xl font-black text-slate-800 dark:text-white mb-3 tracking-tight">${notice.title}</h3>
                                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6 font-medium">
                                        ${notice.content}
                                    </p>

                                    ${expiryDate ? `
                                    <div class="mb-6 flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-500/5 rounded-xl text-red-500 text-[10px] font-bold uppercase tracking-widest">
                                        <span class="material-symbols-outlined text-sm">event_busy</span>
                                        Expires: ${expiryDate}
                                    </div>
                                    ` : ''}
                                    
                                    <div class="pt-6 border-t border-primary/5 flex justify-between items-center text-xs font-bold uppercase tracking-widest text-slate-400">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">person</span>
                                            ${notice.author}
                                        </div>
                                        
                                        ${deleteBtn}
                                    </div>
                                </div>
                            `;
                        });
                    }
                    $("#noticesContainer").html(html);
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }

            complete: function () {
              hideLoader();
            },
        });
    }

    // Modal Control
    $("#addNoticeBtn").click(function() {
        $("#noticeModal").removeClass("hidden");
    });

    $(".close-modal, .modal-overlay").click(function() {
        $("#noticeModal").addClass("hidden");
    });

    // Post Notice
    $("#noticeForm").submit(function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        
        $.ajax({
            url: "../api/notices.php",
            method: "POST",
            data: formData + "&action=create",
            dataType: "json",
            beforeSend: function () {
              showLoader(2);
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Posted!',
                        text: 'Your announcement is live.',
                        confirmButtonColor: '#89986D'
                    });
                    $("#noticeModal").addClass("hidden");
                    $("#noticeForm")[0].reset();
                    loadNotices();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }

            complete: function () {
              hideLoader();
            },
        });
    });

    window.deleteNotice = function(id) {
        Swal.fire({
            title: 'Delete Notice?',
            text: "This announcement will be removed forever.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "../api/notices.php",
                    method: "POST",
                    data: { action: "delete", id: id },
                    dataType: "json",
                    beforeSend: function () {
                      showLoader(2);
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire('Deleted!', response.message, 'success');
                            loadNotices();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }

                    complete: function () {
                      hideLoader();
                    },
                });
            }
        })
    }
});
