import request from '@/utils/request';

export function UploadAvatar(data) {
  return request({
    url: '/profile',
    method: 'post',
    data: data,
  });
}
export function getProfile(query) {
  return request({
    url: '/profile',
    method: 'get',
    params: query,
  });
}
export function update_password(data){
  return request({
    url: 'profile/changepass',
    method: 'put',
    data: data,
  });
}
