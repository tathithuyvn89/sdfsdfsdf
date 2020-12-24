import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/class',
    method: 'get',
    params: query,
  });
}

export function deleteClass(data) {
  return request({
    url: '/class/forcedelete',
    method: 'delete',
    data: data,
  });
}

export function addClass(data) {
  return request({
    url: '/class',
    method: 'post',
    data: data,
  });
}

export function editClass(data) {
  return request({
    url: '/class/' + data.id,
    method: 'put',
    data: data,
  });
}
