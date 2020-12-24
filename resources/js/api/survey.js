import request from '@/utils/request';

export function fetchList(query) {
  return request({
    url: '/survey',
    method: 'get',
    params: query,
  });
}

export function getInformation(query) {
  return request({
    url: '/survey/get/information',
    method: 'get',
    params: query,
  });
}

export function fetchData(query) {
  return request({
    url: '/survey/' + query.survey_id,
    method: 'get',
  });
}

export function addSurvey(data) {
  return request({
    url: '/survey',
    method: 'post',
    data: data,
  });
}

export function editSurvey(data) {
  return request({
    url: '/survey/' + data.get('id') + '?_method=PUT',
    method: 'post',
    data: data,
  });
}

export function deleteSurvey(data) {
  return request({
    url: '/survey/forcedelete',
    method: 'delete',
    data: data,
  });
}
