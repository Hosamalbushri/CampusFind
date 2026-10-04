# تقرير تنفيذ المرحلة 01: سلامة التسليم وإغلاق بلاغات المفقودات

## 1. نتائج فحص المصدر قبل التنفيذ

- كانت `HandoverService::complete()` تنفذ، داخل معاملة التسليم، تحديثاً جماعياً مباشراً على `lost_found_reports` بالاعتماد على هوية مستلم القطعة وفئتها فقط. لم تكن المطابقة تتحقق من أن أي بلاغ يصف القطعة المسلّمة فعلاً.
- كان التحديث يشمل حالتي `draft` و`active`، ويكتب `resolved_found_item_id` ويغلق البلاغات دفعة واحدة، متجاوزاً `ReportStateService` وأحداث Eloquent.
- مخطط المطالبات الحالي لا يحتوي `lost_report_id` أو أي علاقة صريحة موثوقة أخرى بين المطالبة والبلاغ؛ فهو يخزن `found_item_id` و`claimant_student_id` فقط في [مخطط المطالبات](../../packages/CampusFind/LostAndFound/src/Database/Migrations/2026_09_27_000004_create_lost_found_claims_table.php#L11).
- الحقل `resolved_found_item_id` في [مخطط البلاغات](../../packages/CampusFind/LostAndFound/src/Database/Migrations/2026_09_27_000003_create_lost_found_reports_table.php#L17) علامة حل نهائية، وليس رابطاً مؤقتاً أو دليلاً سابقاً للتسليم.
- يجيز [ReportStateService](../../packages/CampusFind/LostAndFound/src/Services/ReportStateService.php#L10) الانتقال `active -> resolved` فقط، ولا يجيز `draft -> resolved`.

## 2. الأسباب الجذرية الدقيقة

1. اعتبار اشتراك بلاغ وقطعة في `student_id + category_id` دليلاً على العلاقة بينهما، مع أن الفئة وهوية الطالب لا تثبتان تطابق القطعة.
2. استخدام `DB::table(...)->update(...)` جماعي بدلاً من انتقال نطاقي على بلاغ محدد.
3. إدراج `draft` في الحالات المستهدفة، مما أنشأ مساراً غير قانوني `draft -> resolved`.
4. عدم وجود قفل صف على البلاغات المستهدفة، لأن العملية لم تكن تحدد بلاغاً واحداً موثوقاً أصلاً.

## 3. الملفات المعدلة

- [HandoverService.php](../../packages/CampusFind/LostAndFound/src/Services/HandoverService.php#L51): أزيلت كتلة الإغلاق الجماعي واستيراد `Schema` المرتبط بها. كانت الكتلة المعيبة تعديلاً موجوداً في شجرة العمل قبل هذه المرحلة؛ أعاد حذفها الملف إلى نسخته المتتبعة في `HEAD`، ولذلك لا يظهر الملف كتعديل نهائي في `git diff` رغم تغير سلوك شجرة العمل عن نقطة البداية.
- [LostAndFoundHandoverPersistenceTest.php](../../packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundHandoverPersistenceTest.php#L112): أضيفت تجهيزات ولقطات صفوف واختبارات الانحدار، ووُسع اختبار فشل المعاملة.
- `docs/lost-found-implementation/PHASE_01_HANDOVER_SAFETY.md`: هذا التقرير.

لم تُعدّل مخططات قاعدة البيانات، أو الواجهة، أو المصادقة، أو بنية الحزم.

## 4. التصحيح المنفذ

أصبحت عملية التسليم تكمل السجل النهائي، وتضيف حدث الحيازة، وتنقل القطعة من `in_custody` إلى `returned` داخل المعاملة الحالية، ثم تتحقق من ثوابت التسليم في [HandoverService](../../packages/CampusFind/LostAndFound/src/Services/HandoverService.php#L118). لا تقرأ العملية أي بلاغ مفقود ولا تقفله ولا تعدله.

هذا هو السلوك الآمن للمرحلة 01 لأن المصدر الحالي لا يملك علاقة صريحة موثوقة بين المطالبة والبلاغ. لم يُستبدل الاستدلال بالفئة بأي استدلال آخر على أساس العنوان أو التاريخ أو الوصف أو هوية الطالب أو درجة التشابه.

## 5. ثوابت الأعمال المطبقة

- لا يتغير أي بلاغ غير مرتبط عند التسليم، بما في ذلك البلاغات النشطة أو المسودات من الفئة نفسها أو فئات أخرى.
- لا تنشئ الموافقة على المطالبة دليلاً زائفاً على تسليم مادي، ولا ينشئ التسليم علاقة زائفة بكل بلاغات المستلم.
- لا يمكن للتسليم تنفيذ `draft -> resolved` أو تعديل بلاغ `cancelled/resolved` لأنه لا ينفذ أي انتقال للبلاغات في هذه المرحلة.
- يستمر التسليم الشرعي في إنشاء سجل تسليم واحد، وإضافة حدث `handed_over`، وإرجاع القطعة، والإبقاء على المطالبة المعتمدة.
- ستظل أي عملية حل شرعية مستقلة خاضعة لـ `ReportStateService`؛ ومسار `resolveReport()` الحالي يستدعيه في [StudentReportApplicationService](../../packages/CampusFind/LostAndFound/src/Services/Application/StudentReportApplicationService.php#L78).

## 6. المعاملة والأقفال

بقيت معاملة `DB::transaction()` كما هي. ما زالت تقفل القطعة، ومطالباتها، وآخر سجل حيازة، والموظف، والطالب المستلم، وتمنع تسليمين متزامنين، كما يظهر في [HandoverService](../../packages/CampusFind/LostAndFound/src/Services/HandoverService.php#L51). بقي تحديث حالة القطعة مشروطاً بقيم الحيازة والحالة السابقة، وأي نتيجة لا تساوي صفاً واحداً تفشل المعاملة.

لا تُقفل بلاغات المفقودات في المرحلة 01؛ فهذا مقصود لأنها لا تُقرأ ولا تُعدّل، ولأن قفل بلاغات غير مرتبطة سيزيد التنافس بلا مبرر. لم تُلتقط استثناءات المعاملة أو تُخفَ؛ تستمر الأخطاء في الانتشار وتؤدي إلى rollback.

## 7. اختبارات الانحدار المضافة أو الموسعة

- **A — بلاغان نشطان من الفئة نفسها:** يثبت الاختبار في [السطر 202](../../packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundHandoverPersistenceTest.php#L202) أن الصفين يبقيان مطابقين تماماً للقطات ما قبل التسليم، مع نجاح إرجاع القطعة.
- **B — حماية المسودة:** يثبت الاختبار في [السطر 217](../../packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundHandoverPersistenceTest.php#L217) بقاء `draft` و`resolved_found_item_id = null` و`closed_at = null`.
- **C — عدة بلاغات غير مرتبطة:** يغطي الاختبار في [السطر 235](../../packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundHandoverPersistenceTest.php#L235) حالات نشطة ومسودات، وفئات وطلاباً مختلفين، ويقارن جميع أعمدة الهوية والعلاقة والحالة والتوقيت قبل العملية وبعدها.
- **D — سلوك التسليم القائم:** بقي اختبار النجاح الحالي ويتحقق من القطعة، وسجل التسليم، والمطالبة، وتسلسل الحيازة، وإفراغ إسقاط الحيازة في [السطر 155](../../packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundHandoverPersistenceTest.php#L155).
- **E — فشل المعاملة:** وُسع الاختبار في [السطر 366](../../packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundHandoverPersistenceTest.php#L366) ليثبت أيضاً بقاء البلاغ كما كان، إضافة إلى rollback لسجل التسليم وحدث الحيازة وحالة القطعة.
- **F — حماية آلة الحالات:** لا يوجد ربط صريح يمكن اختباره بأمان في المرحلة 01. لا ينفذ التسليم أي انتقال بلاغ، بينما تغطي اختبارات `ReportStateMachineTest` الانتقالات القانونية وغير القانونية. اختبار تكامل حل البلاغ المرتبط مؤجل للمرحلة 02.
- **G — التزامن:** قاعدة الاختبار SQLite داخل الذاكرة لا تتحقق بصورة ذات معنى من أقفال الصفوف أو انتظار المعاملات المتزامنة؛ لذلك لا يُدّعى تحقق ذلك عملياً.

لم يُحذف أي اختبار قائم، ولم تُضعف أي مطالبة، ولم يكن هناك اختبار قائم يتوقع الإغلاق الجماعي الخاطئ كي يُستبدل. أضيفت ثلاث حالات جديدة ووُسعت حالة rollback فقط.

## 8. أوامر التحقق والنتائج الفعلية

### الاختبار المستهدف

```bash
LARASEED_OPTIONAL_PACKAGES=student,lost_and_found,web vendor/bin/pest packages/CampusFind/LostAndFound/tests/Feature/LostAndFoundHandoverPersistenceTest.php
```

النتيجة النهائية: **20 اختباراً ناجحاً، 113 assertion، صفر فشل**. كانت نتيجة خط الأساس قبل إضافة الاختبارات **17 اختباراً، 103 assertions**.

### الحزم المطلوبة

```bash
LARASEED_OPTIONAL_PACKAGES=student,lost_and_found,web vendor/bin/pest packages/CampusFind/LostAndFound/tests
LARASEED_OPTIONAL_PACKAGES=student,lost_and_found,web vendor/bin/pest packages/CampusFind/Student/tests
LARASEED_OPTIONAL_PACKAGES=student,lost_and_found,web vendor/bin/pest packages/CampusFind/Web/tests
```

| الحزمة | الاختبارات | Assertions | الفشل |
|---|---:|---:|---:|
| LostAndFound | 318 | 1,811 | 0 |
| Student | 34 | 206 | 0 |
| Web | 24 | 151 | 0 |
| **الإجمالي** | **376** | **2,168** | **0** |

الأرقام التاريخية كانت 373/2,158؛ الزيادة الفعلية هي ثلاثة اختبارات وعشرة assertions.

### التنسيق والتحليل

- `vendor/bin/pint --test packages/.../LostAndFoundHandoverPersistenceTest.php`: ناجح بعد تنسيق ملف الاختبار.
- `php -l` للخدمة وملف الاختبار: ناجح بلا أخطاء تركيبية.
- `composer validate --no-check-publish`: ناجح.
- لا توجد أداة PHPStan مثبتة في `vendor/bin` ولا ملف إعداد PHPStan/Psalm في المستودع، لذلك لم يكن هناك تحليل ساكن مهيأ يمكن تشغيله.
- كشف Pint على `HandoverService.php` مخالفات تنسيق موجودة أيضاً في نسخة `HEAD` (`ordered_imports` ومسافات عوامل النفي). لم تُطبق إعادة تنسيق شاملة على الخدمة حتى لا تُدخل ضوضاء خارج التصحيح الضيق.

## 9. المخاطر المتبقية وحدود التحقق

- لم تُختبر دلالات `SELECT ... FOR UPDATE` أو التنافس الحقيقي على MySQL/PostgreSQL؛ SQLite داخل الذاكرة لا يوفر هذا الإثبات.
- لم يعد التسليم يحل أي بلاغ تلقائياً. هذا مقصود وآمن، لكنه يعني أن حل البلاغ الموافق فعلاً ينتظر وجود علاقة صريحة موثوقة في المرحلة 02.
- توجد تعديلات أخرى كثيرة سابقة في شجرة العمل. لم تُمس ضمن هذا التنفيذ، باستثناء إزالة كتلة العيب المطلوبة من `HandoverService` وإضافة اختبارات هذه المرحلة.

لاختبار التزامن لاحقاً يجب تشغيل اختبار تكامل على محرك الإنتاج الحقيقي باتصالين مستقلين وعمليتين منفصلتين: تُثبت الأولى أقفال القطعة/المطالبة أثناء التسليم، وتحاول الثانية تسليماً منافساً أو تحديث البلاغ المرتبط، مع حواجز زمنية مؤكدة؛ ثم تُفحص النتيجة النهائية وسجل الحيازة والتسليم. عند إضافة الربط الصريح في المرحلة 02 يجب أن يقفل المسار البلاغ المحدد فقط، ويعيد التحقق من العلاقة والحالة بعد القفل، ثم يستدعي `ReportStateService` داخل المعاملة.

## 10. المؤجل صراحة إلى المرحلة 02

- إضافة علاقة صريحة مثل `lost_found_claims.lost_report_id` بعد اعتماد ضماناتها وقواعد التفويض.
- التحقق داخل المعاملة من أن البلاغ مرتبط بالمطالبة المعتمدة وبالطالب والقطعة الصحيحين.
- قفل البلاغ المحدد بـ `lockForUpdate()`، وإعادة قراءة حالته بعد القفل، وتنفيذ `active -> resolved` عبر `ReportStateService`، ثم حفظ `resolved_found_item_id` والسجل التدقيقي المناسب.
- اختبار تكامل الانتقال القانوني والتزامن على MySQL/PostgreSQL.

لم تبدأ هذه المرحلة الثانية، ولم تُضف أي آلية ربط مؤقتة أو migration ناقصة.
