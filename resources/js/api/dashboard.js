import request from '@/utils/request';

export function getResult(query) {
  return request({
    url: '/dashboard/result',
    method: 'get',
    params: query,
  });
}

export function getListUser() {
  return request({
    url: '/dashboard/respondent',
    method: 'get',
  });
}

export function getListSurvey(query) {
  return request({
    url: '/dashboard/survey',
    method: 'get',
    params: query,
  });
}
export function getSurveyDetail(query) {
  return request({
    url: '/dashboard/result/detail',
    method: 'get',
    params: query,
  });
}
