import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/quizz',
    method: 'get',
    params: query,
  });
}

export function fetchData(data) {
  return request({
    url: '/quizz/' + data.id,
    method: 'get',
    data: data,
  });
}

export function addQuiz(data) {
  return request({
    url: '/quizz',
    method: 'post',
    data: data,
  });
}

export function deleteQuiz(data) {
  return request({
    url: '/quizz/forcedelete',
    method: 'delete',
    data: data,
  });
}

export function editQuiz(data) {
  return request({
    url: '/quizz/' + data.get('id') + '?_method=PUT',
    method: 'post',
    data: data,
  });
}
