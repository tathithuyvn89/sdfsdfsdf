import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/tag',
    method: 'get',
    params: query,
  });
}

export function deleteTag(data) {
  return request({
    url: '/tag/forcedelete',
    method: 'delete',
    data: data,
  });
}

export function addTag(data) {
  return request({
    url: '/tag',
    method: 'post',
    data: data,
  });
}

export function editTag(data) {
  return request({
    url: '/tag/' + data.id,
    method: 'put',
    data: data,
  });
}
