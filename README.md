# LkwScheduler

## About

The purpose of this application is to verify that semi truck driver's driving history conforms to the EU regulations (EC) No 561/2006, and to help with planning future driving sessions in a way that the driver won't violate the regulations. This project is still under development. Currently it can validate a series of sessions and tell if it conforms to the rules or not.

Rules can be defined via `Rule` objects (in the future, they can be imported from JSON as well).

## Regulations

- Daily driving period shall not exceed 9 hours, with an exemption of twice a week when it can be extended to 10 hours.
- Total weekly driving time may not exceed 56 hours and the total fortnightly driving time may not exceed 90 hours.
- Daily rest period shall be at least 11 hours, with an exception of going down to 9 hours maximum three times a week. Daily rest can be split into 3 hours rest followed by 9 hour rest to make a total of 12 hours daily rest.
- Breaks of at least 45 minutes (separable into 15 minutes followed by 30 minutes) should be taken after 4 ½ hours at the latest.
- Weekly rest is 45 continuous hours, which can be reduced every second week to 24 hours. Compensation arrangements apply for reduced weekly rest period. Weekly rest is to be taken after six days of working, except for coach drivers engaged in a single occasional service of international transport of passengers who may postpone their weekly rest period after 12 days in order to facilitate coach holidays.
- Daily and/or weekly driving times may be exceeded in exceptional circumstances by up to one hour to enable the driver to reach his/her place of residence or the employer’s operational centre in order to take a weekly rest period. Exceeding the daily and/or weekly driving times by up to two hours is also allowed to enable the driver to reach his/her place of residence or the employer’s operational centre in order to take a regular weekly rest period.

### More info

- [Driving time and rest periods](https://ec.europa.eu/transport/modes/road/social_provisions/driving_time_en)
- [(EC) No 561/2006](https://eur-lex.europa.eu/legal-content/EN/TXT/?uri=CELEX%3A02006R0561-20200820)

## TODO

- [ ] Create `RuleBuilder`
- [ ] Restructure `Rule`
- [ ] Refactor `Validator`
- [ ] Implement `Result` objects to keep track of which `Rule` was applied to which `Session`, and in case of failer, which `Rule` failed to match
- [ ] Create `Scheduler`
- [ ] Create UI

## License

GNU General Public License v3.0

See [LICENSE](LICENSE) to see the full text.
