const routes = [
  // ── Public site ────────────────────────────────────────────────────────────
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '',                   component: () => import('pages/IndexPage.vue')              },
      { path: 'par-mums',           component: () => import('pages/AboutPage.vue')              },
      { path: 'komanda',            component: () => import('pages/TeamPage.vue')               },
      { path: 'notikumi',           component: () => import('pages/EventsPage.vue')             },
      { path: 'kontakti',           component: () => import('pages/ContactPage.vue')            },
      { path: 'noderigi',           redirect: '/noderigi/laukums'                               },
      { path: 'noderigi/laukums',   component: () => import('pages/UsefulSetupPage.vue')        },
      { path: 'noderigi/trenini',   component: () => import('pages/NoderigiTrainingPage.vue')   },
      { path: 'noderigi/checkout',  component: () => import('pages/NoderigiCheckoutPage.vue')   },
      { path: 'noderigi/noteikumi', component: () => import('pages/NoderigiRulesPage.vue')      },
      { path: 'pievienojies',      component: () => import('pages/JoinPage.vue')               },
      { path: 'blog',              component: () => import('pages/BlogListPage.vue')            },
      { path: 'blog/:slug',        component: () => import('pages/BlogPostPage.vue')            },
    ],
  },

  // ── Admin login — standalone (no public nav) ───────────────────────────────
  {
    path: '/admin/login',
    component: () => import('pages/AdminLoginPage.vue'),
    meta: { guestOnly: true },
  },
  // Legacy /login redirect
  { path: '/login', redirect: '/admin/login' },

  // ── Admin panel ─────────────────────────────────────────────────────────────
  {
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    meta: { requiresAdmin: true },
    children: [
      { path: '',          redirect: '/admin/dashboard'                                           },
      { path: 'dashboard',  component: () => import('pages/admin/AdminDashboardPage.vue')      },
      { path: 'players',    component: () => import('pages/admin/AdminPlayersPage.vue')        },
      { path: 'supporters', component: () => import('pages/admin/AdminSupportersPage.vue')     },
      { path: 'requests',   component: () => import('pages/admin/AdminClubRequestsPage.vue')   },
      { path: 'settings',   component: () => import('pages/admin/AdminSettingsPage.vue')       },
      { path: 'blog',       component: () => import('pages/admin/AdminBlogPage.vue')           },
    ],
  },

  // ── 404 ────────────────────────────────────────────────────────────────────
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
