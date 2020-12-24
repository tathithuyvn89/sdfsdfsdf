import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/department',
    method: 'get',
    params: query,
  });
}

export function deleteDepartment(data) {
  return request({
    url: '/department/forcedelete',
    method: 'delete',
    data: data,
  });
}

export function addDepartment(data) {
  return request({
    url: '/department',
    method: 'post',
    data: data,
  });
}

export function editDepartment(data) {
  return request({
    url: '/department/' + data.id,
    method: 'put',
    data: data,
  });
}
