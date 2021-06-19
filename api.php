PRICEAPI_BASE_URL = 'http://api.com/'
TOKEN = 'YOUR-SECURITY-TOKEN';

method get_products(country, source, key, values)
  if job_id = bulk_request(country, source, key, values)
    wait(30 seconds) until bulk_status(job_id) == 'finished'
    return bulk_download(job_id)
  else
    return false
  end
end

method bulk_request(country, source, key, values)
  uri = URI(PRICEAPI_BASE_URL, "/jobs")

  form_data = {
    token: TOKEN,
    country: country,
    source: source,
    key: key,
    values: values.join("\n"),
    completeness: "daily_updated",
    currentness: "one_page"
  }

  body = HTTP.post(uri, form_data)
  json = JSON.parse(body)
  
  if json["success"] == false
    return false
  else
    return json["job_id"]
  end
end

method bulk_status(job_id)
  uri = URI(PRICEAPI_BASE_URL, "/jobs/", job_id, "?token=", TOKEN)

  body = HTTP.get(uri)
  json = JSON.parse(body)
  
  return json["status"]
end

method bulk_download(job_id)
  uri = URI(PRICEAPI_BASE_URL, "/products/bulk/", job_id, ".json?token=", TOKEN)

  body = HTTP.get(uri)
  json = JSON.parse(body)
  
  return json
end
