import request from '@/utils/request';

export function getListSurvey(query) {
  return request({
    url: '/respondent',
    method: 'get',
    params: query,
  });
}

export function getFullInfo(data) {
  return request({
    url: '/informations' + '?la=' + data.la,
    method: 'get',
    data: data,
  });
}

export function getOneSurvey(data) {
  return request({
    url: '/respondent/' + data.id + '?la=' + data.la,
    method: 'get',
    data: data,
  });
}

export function updateStatusSurvey(data) {
  return request({
    url: '/respondent/result',
    method: 'post',
    data: data,
  });
}
