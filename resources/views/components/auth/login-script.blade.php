@props(['users'])

<script>
    function loginData() {
        var allUsers = @json($users);
        return {
            search: '',
            open: false,
            selectedEmail: '',
            selectedName: '',
            selectedNip: '',
            highlightedId: null,
            users: allUsers,
            _closeTimer: null,

            get filteredUsers() {
                var q = this.search.trim().toLowerCase();
                if (q === '') { return this.users; }
                return this.users.filter(function(u) {
                    return u.name.toLowerCase().indexOf(q) !== -1 ||
                        (u.nip && u.nip.indexOf(q) !== -1);
                });
            },

            get adminUsers() {
                return this.filteredUsers.filter(function(u) { return u.role === 'admin'; });
            },

            get pegawaiUsers() {
                return this.filteredUsers.filter(function(u) { return u.role !== 'admin'; });
            },

            get isResultsEmpty() {
                return this.filteredUsers.length === 0;
            },

            get hasResults() {
                return this.filteredUsers.length > 0;
            },

            get hasAdminUsers() {
                return this.adminUsers.length > 0;
            },

            get hasPegawaiUsers() {
                return this.pegawaiUsers.length > 0;
            },

            closeSoon: function() {
                var self = this;
                self._closeTimer = setTimeout(function() {
                    self.open = false;
                }, 160);
            },

            selectUser: function(user) {
                var self = this;
                clearTimeout(self._closeTimer);
                self.selectedEmail = user.email;
                self.selectedName = user.name;
                self.selectedNip = user.nip || '';
                self.search = user.name;
                self.open = false;
                self.highlightedId = null;
                self.$nextTick(function() {
                    var pw = document.getElementById('password');
                    if (pw) { pw.focus(); }
                });
            },

            clearSelection: function() {
                clearTimeout(this._closeTimer);
                this.selectedEmail = '';
                this.selectedName = '';
                this.selectedNip = '';
                this.search = '';
                this.open = false;
                this.highlightedId = null;
            },

            selectFirst: function() {
                if (this.filteredUsers.length > 0) {
                    var target = this.highlightedId
                        ? this.filteredUsers.find(function(u) { return u.id === this.highlightedId; }, this)
                        : null;
                    this.selectUser(target || this.filteredUsers[0]);
                }
            },

            highlightNext: function() {
                var list = this.filteredUsers;
                if (list.length === 0) { return; }
                var idx = list.findIndex(function(u) { return u.id === this.highlightedId; }, this);
                var next = idx < list.length - 1 ? idx + 1 : 0;
                this.highlightedId = list[next].id;
                this.open = true;
            },

            highlightPrev: function() {
                var list = this.filteredUsers;
                if (list.length === 0) { return; }
                var idx = list.findIndex(function(u) { return u.id === this.highlightedId; }, this);
                var prev = idx > 0 ? idx - 1 : list.length - 1;
                this.highlightedId = list[prev].id;
            },

            onSubmit: function(e) {
                if (!this.selectedEmail) {
                    e.preventDefault();
                    this.open = true;
                }
            },
        };
    }
</script>
