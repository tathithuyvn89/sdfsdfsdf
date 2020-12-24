import router from './router';
import store from './store';
import { Message } from 'element-ui';
import NProgress from 'nprogress'; // progress bar
import 'nprogress/nprogress.css'; // progress bar style
import { isLogged } from '@/utils/auth';
import getPageTitle from '@/utils/get-page-title';

NProgress.configure({ showSpinner: false }); // NProgress Configuration

const whiteList = ['', '/login', '/auth-redirect', '/azurelogin']; // no redirect whitelist

router.beforeEach(async(to, from, next) => {
  // start progress bar
  NProgress.start();
  // set page title
  document.title = getPageTitle(to.meta.title);

  // determine whether the user has logged in
  const isUserLogged = isLogged();

  if (isUserLogged) {
    // If the user is logged in => Has Token
    if (to.path === '/login' || to.path === '/') {
      // If user logged in, redirect to dashboard
      next({ path: '/dashboard/index' });
      NProgress.done();
    } else {
      // determine whether the user has obtained his permission roles through getInfo
      const hasRoles = store.getters.roles && store.getters.roles.length > 0;

      if (hasRoles) {
        next();
      } else {
        try {
          // get user info
          // note: roles must be a object array! such as: ['admin'] or ,['manager','respondent']
          const { roles, permissions } = await store.dispatch('user/getInfo');

          // generate accessible routes map based on roles
          const accessRoutes = await store.dispatch('permission/generateRoutes', { roles, permissions });
          router.addRoutes(accessRoutes);
          next({ ...to, replace: true });
        } catch (error) {
          // remove token and go to login page to re-login
          await store.dispatch('user/resetToken');
          Message.error(error.message || 'Has Error');
          next(`?redirect=${to.path}`);
          NProgress.done();
        }
      }
    }
  } else {
    // If the user is not logged in => Has No Token
    if (whiteList.indexOf(to.matched[0] ? to.matched[0].path : '') !== -1) {
      // in the free login whitelist, go directly
      next();
    } else {
      // other pages that do not have permission to access are redirected to the login page.
      next(`?redirect=${to.path}`);
      NProgress.done();
    }
  }
});

router.afterEach(() => {
  // finish progress bar
  NProgress.done();
});
