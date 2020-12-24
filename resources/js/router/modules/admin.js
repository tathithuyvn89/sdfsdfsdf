/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout';

const adminRoutes = {
  path: '/administrator',
  component: Layout,
  redirect: '/administrator/users',
  name: 'Administrator',
  alwaysShow: true,
  meta: {
    title: 'administrator',
    icon: 'admin',
    permissions: ['view menu administrator'],
  },
  children: [
    /** User managements */
    {
      path: '/dashboard/quiz',
      redirect: '/dashboard/quiz/index',
      meta: {
        permissions: ['view menu quizz'],
      },
      children: [
        {
          path: 'index',
          component: () => import('@/views/Quiz/index'),
          meta: {
            title: 'listquiz',
            icon: 'documentation',
          },
        },
      ],
    },
    {
      path: '/dashboard/survey',
      redirect: '/dashboard/survey/index',
      meta: {
        permissions: ['view menu survey'],
      },
      children: [
        {
          path: 'index',
          component: () => import('@/views/Survey/index'),
          meta: {
            title: 'listsurvey',
            icon: 'form',
          },
        },
      ],
    },
    {
      path: '/dashboard/users',
      component: () => import('@/views/users/List'),
      name: 'UserList1',
      meta: { title: 'users', icon: 'user', permissions: ['manage user'] },
    },
    /** Role and permission */
    {
      path: '/dashboard/roles',
      component: () => import('@/views/role-permission/List'),
      name: 'RoleList1',
      meta: { title: 'rolePermission', icon: 'role', permissions: ['manage permission'] },
      hidden: true,
    },
    {
      path: '/dashboard/settings',
      redirect: 'noredirect',
      meta: {
        permissions: ['view menu setting'],
        title: 'settings',
        icon: 'admin',
      },
      children: [
        {
          path: 'department',
          component: () => import('@/views/Settings/Department/index'),
          meta: {
            permissions: ['view menu department'],
            title: 'department',
          },
        },
        {
          path: 'class',
          component: () => import('@/views/Settings/Class/index'),
          meta: {
            permissions: ['view menu class'],
            title: 'class',
          },
        },
        {
          path: 'tag',
          component: () => import('@/views/Settings/Tag/index'),
          meta: {
            permissions: ['view menu tag'],
            title: 'tag',
          },
        },
      ],
    },
  ],
};

export default adminRoutes;
